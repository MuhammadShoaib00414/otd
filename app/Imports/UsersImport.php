<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class UsersImport implements WithMultipleSheets 
{
   
    public function sheets(): array
    {
        return [
            new FirstSheetImport(),
            new SecondSheetImport(),
        ];
    }
    
    public function onUnknownSheet($sheetName)
    {
        // E.g. you can log that a sheet was not found.
        info("Sheet {$sheetName} was skipped");
    }
}