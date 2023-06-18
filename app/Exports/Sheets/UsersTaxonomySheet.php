<?php

namespace App\Exports\Sheets;

use Generator;
use App\Category;
use App\Segment;
use App\Taxonomy;
use App\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Row;

class UsersTaxonomySheet
{   
    private $user_ids;
    private $isFirst;
    private $folder;
    private $file;
    private $taxonomy;

    public function __construct($isFirst, $user_ids, $folder, $taxonomy)
    {
        $this->isFirst = $isFirst;
        $this->user_ids = $user_ids;
        $this->folder = $folder;
        $this->taxonomy = $taxonomy;

        $this->startExport();
    }

    public function startExport()
    {
        $path = $this->folder . '/UserCategories - ' . explode('/', $this->taxonomy->name)[0] .'.csv';

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
        $options = $this->taxonomy->options()->cursor();

        foreach ($options as $option) {
            if ($user->options()->where('options.id', $option->id)->exists())
                $row[] = 'true';
            else
                $row[] = '';
        }

        return $row;
    }

    public function headings(): array
    {
        $headings = ['id', 'name'];
        $options = $this->taxonomy->options()->cursor();

        foreach ($options as $option) {
            $headings[] = $option->name;
        }

        return $headings;
    }
}
