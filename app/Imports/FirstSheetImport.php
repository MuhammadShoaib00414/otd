<?php



namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\HasReferencesToOtherSheets;
use Maatwebsite\Excel\Concerns\ToCollection;

class FirstSheetImport implements ToCollection, HasReferencesToOtherSheets
{
    public function collection(Collection $rows)
    {
        //
    }
}