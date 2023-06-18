<?php

namespace App\Exports;

use App\Budget;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;

class BudgetsExport implements FromQuery, WithMapping, WithHeadings
{
    public function query()
    {
        return Budget::query();
    }

    public function map($budget): array
    {
        return [
            $budget->id,
            $budget->year,
            $budget->quarter,
            (optional(optional($budget)->group)->name) ?: 'DELETED',
            $budget->total_budget/100,
            $budget->spent/100,
            $budget->remaining/100,
        ];
    }

    public function headings(): array
    {
        return [
            'id',
            'year',
            'quarter',
            'group',
            'allocated',
            'spent',
            'remaining',
        ];
    }
}