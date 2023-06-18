<?php

namespace App\Exports\Sheets;

use Generator;
use App\Question;
use App\Segment;
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

class UsersQuestionsSheet
{   
    private $user_ids;
    private $folder;
    private $writer;
    private $questions;
    private $isFirst;

    public function __construct($user_ids, $folder, $isFirst)
    {
        $this->user_ids = $user_ids;
        $this->folder = $folder;
        $this->questions = Question::where('is_enabled', 1)->get();
        $this->isFirst = $isFirst;

        $this->startExport();
    }

    public function startExport()
    {
        $path = $this->folder . '/UserQuestions.csv';

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

        foreach($this->questions as $question)
        {
            if (optional(optional($user->questions()->where('questions.id', $question->id)->first())->pivot)->answer)
                $row[] = optional(optional($user->questions()->where('questions.id', $question->id)->first())->pivot)->answer;
            else
                $row[] = '';
        }

        return $row;
    }

    public function headings(): array
    {
        $headings = ['id', 'name'];

        foreach($this->questions as $question)
            $headings[] = $question->prompt;

        return $headings;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Custom Questions';
    }
}
