<?php

namespace App\Http\Controllers;

use App\Exports\IncomeReportExport;
use App\Models\ElectricBill;
use App\Models\LeasePayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function incomeReport(Request $request)
    {
        $viewType = $request->get('view_type', 'month');
        $dateInput = $request->get('date');

        if (! $dateInput) {
            $dateInput = ($viewType === 'year') ? now()->format('Y') : now()->format('Y-m');
        }

        try {
            $parseDate = ($viewType === 'year' && strlen($dateInput) === 4) ? $dateInput.'-01-01' : $dateInput;
            $currentDate = Carbon::parse($parseDate);
        } catch (\Exception $e) {
            $currentDate = now();
        }

        if ($viewType === 'year') {
            $prevDate = $currentDate->copy()->subYear()->format('Y');
            $nextDate = $currentDate->copy()->addYear()->format('Y');
            $displayDate = $currentDate->format('Y');
        } else {
            $prevDate = $currentDate->copy()->subMonth()->format('Y-m');
            $nextDate = $currentDate->copy()->addMonth()->format('Y-m');
            $displayDate = $currentDate->format('F Y');
        }

        $paidQuery = LeasePayment::with(['lease.tenant', 'lease.room'])
            ->where('status', 'paid');

        if ($viewType === 'year') {
            $paidQuery->whereYear('paid_at', $currentDate->year);
        } else {
            $paidQuery->whereYear('paid_at', $currentDate->year)
                ->whereMonth('paid_at', $currentDate->month);
        }

        $payments = $paidQuery->orderBy('paid_at', 'desc')->get();

        $pendingQuery = LeasePayment::where('status', '!=', 'paid');
        if ($viewType === 'year') {
            $pendingQuery->whereYear('due_date', $currentDate->year);
        } else {
            $pendingQuery->whereYear('due_date', $currentDate->year)
                ->whereMonth('due_date', $currentDate->month);
        }
        $pendingPayments = $pendingQuery->get();

        $billQuery = ElectricBill::query();
        if ($viewType === 'year') {
            $billQuery->whereYear('billing_month', $currentDate->year);
        } else {
            $billQuery->whereYear('billing_month', $currentDate->year)
                ->whereMonth('billing_month', $currentDate->month);
        }
        $actualUtilityExpense = $billQuery->sum('total_amount');

        $totalRent = $payments->sum('amount');
        $totalElectricCollected = $payments->sum('electric_bill_amount') + $payments->sum('carried_over_debt');
        $grandTotal = $totalRent + $totalElectricCollected;

        $totalReceivables = $pendingPayments->sum('amount') +
                           $pendingPayments->sum('electric_bill_amount') +
                           $pendingPayments->sum('carried_over_debt');

        $totalProfit = $grandTotal - $actualUtilityExpense;

        if ($viewType === 'year') {
            $groupedPayments = $payments->groupBy(fn ($p) => $p->paid_at->format('F'));
        } else {
            $groupedPayments = $payments->isEmpty() ? [] : [$displayDate => $payments];
        }

        return view('reports.income', compact(
            'groupedPayments', 'viewType', 'dateInput', 'prevDate', 'nextDate',
            'displayDate', 'totalRent', 'totalElectricCollected', 'grandTotal',
            'actualUtilityExpense', 'totalProfit', 'totalReceivables'
        ));
    }

    public function downloadIncomeExcel(Request $request)
    {
        $viewType = $request->get('view_type', 'month');
        $dateInput = $request->get('date', now()->format('Y-m'));

        $filename = "Income_Report_{$viewType}_{$dateInput}.xlsx";

        return Excel::download(new IncomeReportExport($viewType, $dateInput), $filename);
    }
}
