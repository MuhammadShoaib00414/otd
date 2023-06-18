<?php

namespace App\Exports\Sheets;

use Generator;
use App\User;
use App\Log;
use App\Segment;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromGenerator;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Row;

class UsersActivitySheet
{   
    private $logs;
    private $folder;
    private $file;
    private $isFirst;

    public function __construct($logs, $folder, $isFirst)
    {
        $this->logs = $logs;
        $this->folder = $folder;
        $this->isFirst = $isFirst;

        $this->startExport();
    }

    public function startExport()
    {
        $path = $this->folder . '/UserActivity.csv';

        //delete the existing csv if it exists
        if($this->isFirst && file_exists($path))
            unlink($path);

        $this->file = fopen($path, 'a');

        if($this->isFirst)
            $this->addRow($this->headings());

        foreach($this->logs as $log)
            $this->addRow($this->map($log));

        fclose($this->file);
    }

    public function addRow($rowData)
    {
        fputcsv($this->file, $rowData);
    }

    public function map($log): array
    {
        return [
            $log->id,
            $log->action,
            $log->user->id,
            $log->user->name,
            $log->message,
            $log->created_at->format('Y-m-d'),
        ];
    }

    public function headings(): array
    {
        return [
            'id',
            'action',
            'user id',
            'user',
            'message',
            'timestamp',
        ];
    }
}
