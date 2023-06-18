<?php

namespace App\Exports\Sheets;

use Generator;
use App\User;
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

class UsersSheet
{
    private $user_ids;
    private $folder;
    private $file;
    private $isFirst;

    public function __construct($user_ids, $folder, $isFirst)
    {
        $this->user_ids = $user_ids;
        $this->folder = $folder;

        $this->startExport();
    }

    public function startExport()
    {
        $path = $this->folder . '/Users.csv';

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
       $userObj = [
            $user->id,
            $user->name,
            $user->email,
            ($user->is_admin) ? 'true' : '',
            $user->created_at ? $user->created_at->format('Y-m-d') : '',
            $user->job_title,
            $user->summary ?: '',
            $user->company ?: '',
            $user->location ?: '',
            $user->twitter ?: '',
            $user->instagram ?: '',
            $user->facebook ?: '',
            $user->linkedin ?: '',
            $user->website ?: '',
            (!$user->is_enabled) ? 'true' : '',
            $user->superpower ?: '',
            $user->points_total ?: '',
            ($user->is_mentor) ? 'true' : '',
        ];

        foreach(\App\Title::cursor() as $title) {
            if ($user->titles()->where('id', $title->id)->first())
                $userObj[] = $user->titles()->where('id', $title->id)->first()->pivot->assigned->name;
            else
                $userObj[] = '';
        }

        return $userObj;
    }

    public function headings(): array
    {
        $headings = [
            'id',
            'name',
            'email',
            'admin',
            'join date',
            'job title',
            'summary',
            'company',
            'location',
            'twitter',
            'instagram',
            'facebook',
            'linkedin',
            'website',
            'disabled',
            'superpower',
            'total points',
            'mentor status',
        ];

        foreach(\App\Title::cursor() as $title) {
            $headings[] = $title->name ?: '';
        }

        return $headings;
    }
}
