<?php

namespace App\Exports;

use App\ArticlePost;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class ContentExport implements FromCollection, WithMapping, WithHeadings, WithColumnFormatting
{
	public function __construct($start_date, $end_date)
	{
		$this->start_date = $start_date;
		$this->end_date = $end_date;
	}

    public function collection()
    {
        $articles = ArticlePost::query();

        if($this->start_date)
            $articles = $articles->whereDate('created_at', '>=', $this->start_date);
        if($this->end_date)
            $articles = $articles->whereDate('created_at', '<=', $this->end_date);

        return $articles->orderBy('created_at', 'asc')->get();
    }

    public function map($article): array
    {
    	$articleObj = [
    		$article->id,
    		$article->title,
    		$article->created_at->format('d/m/Y'),
    		$article->clicks,
    	];

        if($article->listing()->exists() && $article->listing->groups()->exists())
            $groups = $article->listing->groups()->pluck('name');
        else
            $groups = collect();

        if($article->listing()->exists() && $article->listing->group()->exists() && !$groups->contains($article->listing->group->name))
                $groups->concat([$article->listing->group->name]);

        if($groups->count())
            $articleObj[] = $groups->implode(',');

    	return $articleObj;
    }

    public function headings(): array
    {
    	return [
    		'id',
    		'title',
    		'date',
    		'clicks',
    		'groups',
    	];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
