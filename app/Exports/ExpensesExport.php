<?php

namespace App\Exports;

use App\Expense;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class ExpensesExport implements FromCollection, WithMapping, WithHeadings, WithColumnFormatting
{
    public $budget;

    public function __construct($budget)
    {
        $this->budget = $budget;
    }

    public function collection()
    {
        return $this->budget->expenses;
    }

    public function map($expense): array
    {
        return [
            $expense->id,
            $expense->date,
            ($expense->category) ? $expense->category->name : 'Uncategorized',
            $expense->description,
            $expense->user->name,
            $expense->amount/100,
        ];
    }

    public function headings(): array
    {
        return [
            'id',
            'date',
            'category',
            'description',
            'added by',
            'amount',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}