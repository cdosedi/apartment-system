<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class IncomeReportExport implements WithMultipleSheets
{
    protected $viewType;

    protected $dateInput;

    public function __construct($viewType, $dateInput)
    {
        $this->viewType = $viewType;
        $this->dateInput = $dateInput;
    }

    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new IncomeDetailedSheet($this->viewType, $this->dateInput);

        if ($this->viewType === 'year') {
            $sheets[] = new IncomeSummarySheet($this->dateInput);
        }

        return $sheets;
    }
}
