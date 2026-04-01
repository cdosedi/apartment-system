<?php

namespace App\Exports;

use App\Models\ElectricBill;
use App\Models\LeasePayment;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class IncomeDetailedSheet implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $viewType;

    protected $dateInput;

    public function __construct($viewType, $dateInput)
    {
        $this->viewType = $viewType;
        $this->dateInput = $dateInput;
    }

    public function title(): string
    {
        return 'Detailed Transactions';
    }

    public function collection()
    {
        $parseDate = ($this->viewType === 'year' && strlen($this->dateInput) === 4) ? $this->dateInput.'-01-01' : $this->dateInput;
        $date = Carbon::parse($parseDate);

        $query = LeasePayment::with(['lease.tenant', 'lease.room', 'receipt'])->where('status', 'paid');
        $this->viewType === 'year' ? $query->whereYear('paid_at', $date->year) : $query->whereYear('paid_at', $date->year)->whereMonth('paid_at', $date->month);
        $payments = $query->orderBy('paid_at', 'asc')->get();

        $billQuery = ElectricBill::query();
        $this->viewType === 'year' ? $billQuery->whereYear('billing_month', $date->year) : $billQuery->whereYear('billing_month', $date->year)->whereMonth('billing_month', $date->month);
        $totalExpense = $billQuery->sum('total_amount');

        $pendingQuery = LeasePayment::where('status', '!=', 'paid');
        $this->viewType === 'year' ? $pendingQuery->whereYear('due_date', $date->year) : $pendingQuery->whereYear('due_date', $date->year)->whereMonth('due_date', $date->month);
        $receivables = $pendingQuery->get()->sum(fn ($p) => $p->amount + $p->electric_bill_amount + $p->carried_over_debt);

        $rentSum = $payments->sum('amount');
        $utilSum = $payments->sum('electric_bill_amount') + $payments->sum('carried_over_debt');

        $payments->push((object) ['is_spacer' => true]);
        $payments->push((object) ['label' => 'TOTAL RENT COLLECTED', 'val' => $rentSum, 'is_stat' => true]);
        $payments->push((object) ['label' => 'TOTAL UTILITY RECOVERY', 'val' => $utilSum, 'is_stat' => true]);
        $payments->push((object) ['label' => 'ACTUAL UTILITY EXPENSE', 'val' => $totalExpense, 'is_stat' => true, 'is_neg' => true]);
        $payments->push((object) ['label' => 'TOTAL RECEIVABLES (UNPAID)', 'val' => $receivables, 'is_stat' => true]);
        $payments->push((object) ['label' => 'NET PROFIT', 'val' => ($rentSum + $utilSum - $totalExpense), 'is_stat' => true, 'is_bold' => true]);

        return $payments;
    }

    public function headings(): array
    {
        return ['Date Settled', 'Tenant Name', 'Room', 'Rent Component', 'Utility Recovery', 'Gross Total', 'Receipt #'];
    }

    public function map($p): array
    {
        if (isset($p->is_spacer)) {
            return ['', '', '', '', '', '', ''];
        }

        if (isset($p->is_stat)) {
            return [$p->label, '', '', '', '', $p->val, ''];
        }

        return [
            $p->paid_at->format('M d, Y'),
            $p->lease->tenant->full_name,
            $p->lease->room->room_number,
            $p->amount,
            ($p->electric_bill_amount + $p->carried_over_debt),
            ($p->amount + $p->electric_bill_amount + $p->carried_over_debt),
            $p->receipt->receipt_number ?? 'N/A',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '000000']]],
            ($lastRow) => ['font' => ['bold' => true]],
        ];
    }
}
