<?php

namespace App\Http\Controllers;

use App\Models\ElectricBill;
use App\Models\Lease;
use App\Models\LeasePayment;
use App\Models\Room;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        $totalBeds = Room::sum('bed_capacity');
        $occupiedBeds = Lease::where('status', 'active')->count();
        $occupancyRate = $totalBeds > 0 ? ($occupiedBeds / $totalBeds) * 100 : 0;

        $collections = LeasePayment::where('status', 'paid')
            ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
            ->get();

        $totalRent = $collections->sum('amount');
        $totalElectricCollected = $collections->sum('electric_bill_amount') + $collections->sum('carried_over_debt');
        $thisMonthTotal = $totalRent + $totalElectricCollected;

        $actualUtilityExpense = ElectricBill::whereYear('billing_month', $today->year)
            ->whereMonth('billing_month', $today->month)
            ->sum('total_amount');

        $totalProfit = $thisMonthTotal - $actualUtilityExpense;

        $totalReceivables = LeasePayment::whereIn('status', ['pending', 'overdue'])
            ->whereYear('due_date', now()->year)
            ->whereMonth('due_date', now()->month)
            ->get()
            ->sum(fn ($p) => $p->amount + $p->electric_bill_amount + $p->carried_over_debt);

        $topDelinquents = LeasePayment::whereIn('status', ['pending', 'overdue'])
            ->where('due_date', '<', $today)
            ->with(['lease.tenant', 'lease.room'])
            ->get()
            ->groupBy('lease.tenant_id')
            ->map(function ($group) {
                $first = $group->first();

                return (object) [
                    'tenant_name' => $first->lease->tenant->full_name,
                    'room_number' => $first->lease->room->room_number ?? 'N/A',
                    'total_debt' => $group->sum(fn ($p) => $p->amount + $p->electric_bill_amount + $p->carried_over_debt),
                    'missed_count' => $group->count(),
                ];
            })
            ->sortByDesc('total_debt')->take(10);

        $utilityLossRooms = [];
        $currentBills = ElectricBill::whereMonth('billing_month', $today->month)
            ->whereYear('billing_month', $today->year)
            ->with('room')
            ->get();

        foreach ($currentBills as $bill) {
            $recovered = LeasePayment::where('electric_bill_id', $bill->id)
                ->where('status', 'paid')
                ->sum('electric_bill_amount');

            if ($recovered < $bill->total_amount) {
                $utilityLossRooms[] = (object) [
                    'room_number' => $bill->room->room_number,
                    'loss' => $bill->total_amount - $recovered,
                ];
            }
        }

        $lastSixMonths = collect();
        $ytdData = collect();
        for ($i = 5; $i >= 0; $i--) {
            $m = $today->copy()->subMonths($i);
            $p = LeasePayment::where('status', 'paid')
                ->whereYear('paid_at', $m->year)->whereMonth('paid_at', $m->month)->get();
            $rent = $p->sum('amount');
            $util = $p->sum('electric_bill_amount') + $p->sum('carried_over_debt');
            $exp = ElectricBill::whereYear('billing_month', $m->year)->whereMonth('billing_month', $m->month)->sum('total_amount');

            $lastSixMonths->push(['month' => $m->format('M'), 'rent' => $rent, 'utilities' => $util]);
            $ytdData->push(['month' => $m->format('M'), 'profit' => ($rent + $util) - $exp]);
        }

        $roomMap = Room::withCount(['activeLeases' => fn ($q) => $q->where('status', 'active')])
            ->orderByRaw('CAST(room_number AS UNSIGNED) ASC')->get();

        return view('dashboard', compact(
            'occupiedBeds', 'totalBeds', 'occupancyRate',
            'totalRent', 'totalElectricCollected', 'actualUtilityExpense',
            'totalProfit', 'totalReceivables', 'topDelinquents',
            'utilityLossRooms', 'lastSixMonths', 'ytdData', 'roomMap'
        ));
    }
}
