<?php

namespace App\Exports;

use App\Export;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Exports\Sheets\UsersActivitySheet;
use App\Exports\Sheets\UsersGroupsSheet;
use App\Exports\Sheets\UsersQuestionsSheet;
use App\Exports\Sheets\UsersSheet;
use App\Exports\Sheets\UsersTaxonomySheet;
use App\Segment;
use App\Taxonomy;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SegmentExport implements WithMultipleSheets
{
    use Exportable;

    protected $segment;
    
    public function __construct(Segment $segment)
    {
        $this->segment = $segment;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $user_ids = $this->segment->user_ids;

        $sheets[] = new UsersSheet($user_ids);
        $sheets[] = new UsersGroupsSheet($user_ids);
        foreach(Taxonomy::where('is_enabled', 1)->cursor() as $taxonomy) {
            $sheets[] = new UsersTaxonomySheet($taxonomy, $user_ids);
        }
        $sheets[] = new UsersQuestionsSheet($user_ids);
        $sheets[] = new UsersActivitySheet($user_ids);

        return $sheets;
    }
}
