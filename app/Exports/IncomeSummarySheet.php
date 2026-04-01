<?php

namespace App\Exports;

use App\Models\ElectricBill;
use App\Models\LeasePayment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class IncomeSummarySheet implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    protected $year;

    public function __construct($year)
    {
        $this->year = $year;
    }

    public function title(): string
    {
        return 'Annual Summary';
    }

    public function collection()
    {
        $payments = LeasePayment::where('status', 'paid')->whereYear('paid_at', $this->year)->get();
        $bills = ElectricBill::whereYear('billing_month', $this->year)->get();

        $summary = $payments->groupBy(fn ($p) => $p->paid_at->format('F'))
            ->map(function ($monthGroup, $monthName) use ($bills) {
                $rent = $monthGroup->sum('amount');
                $recovery = $monthGroup->sum('electric_bill_amount') + $monthGroup->sum('carried_over_debt');

                $expense = $bills->filter(fn ($b) => $b->billing_month->format('F') === $monthName)->sum('total_amount');

                return [
                    'Month' => $monthName,
                    'Rent' => $rent,
                    'Utility Recovery' => $recovery,
                    'Actual Expense' => $expense,
                    'Net Profit' => ($rent + $recovery) - $expense,
                ];
            })->values();

        return $summary;
    }

    public function headings(): array
    {
        return ['Month', 'Total Rent', 'Utility Recovery', 'Actual Utility Expense', 'Net Profit'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4B5563']]],
        ];
    }
}
