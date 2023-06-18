<?php

namespace App\Exports;

use App\Event;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RsvpExport implements FromCollection, WithMapping, WithHeadings, WithColumnWidths, WithStyles
{

    public function __construct(Event $event)
    {
        $this->event = $event;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->event->eventRsvps()->where('response', 'yes')->get();
    }

    public function map($rsvp): array
    {
        return [
            $rsvp->user->name,
            $rsvp->user->email,
        ];
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 50,            
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
