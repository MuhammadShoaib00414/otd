<?php

namespace App\Exports\Sheets;

use Generator;
use App\User;
use App\Group;
use App\Segment;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Row;

class UsersGroupsSheet
{
    private $user_ids;
    private $folder;
    private $file;
    private $isFirst;

    public function __construct($user_ids, $folder, $isFirst)
    {
        $this->user_ids = $user_ids;
        $this->folder = $folder;
        $this->isFirst = $isFirst;

        $this->startExport();
    }

    public function startExport()
    {
        $path = $this->folder . '/UserGroups.csv';

        //delete the existing csv if it exists
        if($this->isFirst && file_exists($path))
            unlink($path);

        $this->file = fopen($path, 'a');

        if($this->isFirst)
            $this->addRow($this->headings());

        User::query()->whereIn('id', $this->user_ids)->visible()->each(function($user) {
           $this->addRow($this->map($user));
        });

        fclose($this->file);

    }

    public function addRow($rowData)
    {
        fputcsv($this->file, $rowData);
    }

    public function map($user): array
    {
        $row = [$user->id, $user->name];
        $groups = Group::orderBy('name', 'asc')->cursor();

        foreach ($groups as $group) {
            if ($user->groups()->where('id', $group->id)->exists())
                $row[] = 'true';
            else
                $row[] = '';
        }

        return $row;
    }

    public function headings(): array
    {
        $headings = ['id', 'name'];
        $groups = Group::orderBy('name', 'asc')->cursor();

        foreach ($groups as $group) {
            $headings[] = $group->name;
        }

        return $headings;
    }
}
