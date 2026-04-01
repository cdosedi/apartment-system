<?php

namespace App\Http\Controllers;

use App\Models\ElectricBill;
use App\Models\Lease;
use App\Models\LeasePayment;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ElectricBillController extends Controller
{
    public function index()
    {

        $rooms = Room::with(['activeLeases' => function ($q) {
            $q->with('tenant')->where('status', 'active');
        }])
            ->orderByRaw('CAST(room_number AS UNSIGNED)')
            ->paginate(24);

        return view('electric-bills.index', compact('rooms'));
    }

    public function roomBills(Room $room)
    {
        $bills = ElectricBill::with(['room', 'leasePayments.lease.tenant'])
            ->where('room_id', $room->id)
            ->orderBy('billing_month', 'desc')
            ->paginate(10);

        return view('electric-bills.room-bills', compact('room', 'bills'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required',
            'billing_month' => 'required|date_format:Y-m',
            'total_amount' => 'required|numeric|min:1',
        ]);

        $room = Room::findOrFail($request->room_id);
        $billingStart = Carbon::parse($request->billing_month)->startOfMonth();
        $billingEnd = $billingStart->copy()->endOfMonth();

        if (ElectricBill::where('room_id', $room->id)->where('billing_month', $billingStart)->exists()) {
            return back()->withErrors(['message' => 'Bill already exists for this period.']);
        }

        DB::transaction(function () use ($billingStart, $billingEnd, $room, $request) {
            $electricBill = ElectricBill::create([
                'room_id' => $room->id,
                'billing_month' => $billingStart,
                'total_amount' => $request->total_amount,
            ]);

            $this->calculateAndApplyBills($room, $billingStart, $billingEnd, $electricBill);
        });

        return back()->with('success', 'Electric bill created successfully.');
    }

    public function update(Request $request, ElectricBill $bill)
    {
        $request->validate(['total_amount' => 'required|numeric|min:1']);

        DB::transaction(function () use ($bill, $request) {
            $oldPayments = LeasePayment::where('electric_bill_id', $bill->id)->get();
            foreach ($oldPayments as $lp) {
                if ($lp->carried_over_debt > 0) {
                    $lp->lease->increment('pending_electric_debt', $lp->carried_over_debt);
                }

                if ($lp->electric_bill_amount == 0) {

                    $lp->lease->update(['pending_electric_debt' => 0]);
                }
            }

            LeasePayment::where('electric_bill_id', $bill->id)->update([
                'electric_bill_amount' => 0,
                'carried_over_debt' => 0,
                'electric_bill_id' => null,
            ]);

            $bill->update(['total_amount' => $request->total_amount]);

            $billingStart = Carbon::parse($bill->billing_month)->startOfMonth();
            $billingEnd = $billingStart->copy()->endOfMonth();

            $this->calculateAndApplyBills($bill->room, $billingStart, $billingEnd, $bill);
        });

        return back()->with('success', 'Bill updated and debt preserved.');
    }

    protected function calculateAndApplyBills($room, $billingStart, $billingEnd, $electricBill)
    {
        $eligibleLeases = Lease::where('room_id', $room->id)
            ->where('start_date', '<=', $billingEnd)
            ->where('end_date', '>=', $billingStart)
            ->get();

        if ($eligibleLeases->isEmpty()) {
            return;
        }

        $totalBedDays = 0;
        foreach ($eligibleLeases as $lease) {
            $start = Carbon::parse($lease->start_date)->max($billingStart);
            $end = Carbon::parse($lease->end_date)->min($billingEnd);
            $totalBedDays += $start->diffInDays($end) + 1;
        }

        $totalBill = $electricBill->total_amount;
        $costPerDay = $totalBedDays > 0 ? ($totalBill / $totalBedDays) : 0;

        foreach ($eligibleLeases as $lease) {
            $start = Carbon::parse($lease->start_date)->max($billingStart);
            $end = Carbon::parse($lease->end_date)->min($billingEnd);
            $days = $start->diffInDays($end) + 1;

            $currentShare = round($costPerDay * $days, 2);

            $payment = LeasePayment::where('lease_id', $lease->id)
                ->whereYear('due_date', $billingStart->year)
                ->whereMonth('due_date', $billingStart->month)
                ->first();

            if (! $payment) {
                $payment = LeasePayment::where('lease_id', $lease->id)
                    ->orderBy('due_date', 'desc')
                    ->first();
            }

            if (! $payment) {
                continue;
            }

            $isMoveInMonth = Carbon::parse($lease->start_date)->isSameMonth($billingStart);
            $isPaidAdvance = ($payment->status === 'paid');

            if ($isMoveInMonth || $isPaidAdvance) {

                $lease->increment('pending_electric_debt', $currentShare);

                $payment->update([
                    'electric_bill_amount' => 0,
                    'carried_over_debt' => 0,
                    'electric_bill_id' => $electricBill->id,
                ]);
            } else {

                $debtToCollect = $lease->pending_electric_debt;

                $payment->update([
                    'electric_bill_amount' => $currentShare,
                    'carried_over_debt' => $debtToCollect,
                    'electric_bill_id' => $electricBill->id,
                ]);

                $lease->update(['pending_electric_debt' => 0]);
            }
        }
    }
}
