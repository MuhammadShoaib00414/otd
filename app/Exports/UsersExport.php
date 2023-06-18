<?php

namespace App\Exports;

use App\User;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class UsersExport implements FromQuery, WithMapping, WithHeadings, WithColumnFormatting
{
    public function query()
    {
        return User::cursor();
    }

    public function map($user): array
    {
        $userObj = [
            $user->id,
            $user->name,
            $user->email,
            ($user->is_admin) ? 'true' : '',
            Date::dateTimeToExcel($user->created_at),
            $user->job_title,
            $user->summary,
            $user->company,
            $user->location,
            $user->twitter,
            $user->instagram,
            $user->facebook,
            $user->linkedin,
            $user->website,
            (!$user->is_enabled) ? 'true' : '',
            $user->superpower,
            $user->points_total,
            ($user->is_mentor) ? 'true' : '',
        ];

        foreach(\App\Title::all() as $title) {
            if ($user->titles->where('id', $title->id)->first())
                $userObj[] = $user->titles->where('id', $title->id)->first()->pivot->assigned->name;
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

        foreach(\App\Title::all() as $title) {
            $headings[] = $title->name;
        }

        return $headings;
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_DATE_DATETIME,
        ];
    }
}