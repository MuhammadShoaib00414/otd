<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserQueryController extends Controller
{
    public function process(Request $request)
    {
        $filters = json_decode($request->input('query'));
        $eloquent = User::query();
        $this->buildForFilterGroup($eloquent, $filters);

        return response()->json($eloquent->pluck('id'));
    }

    protected function buildForFilterGroup($query, $group)
    {
        $children = collect($group->children);
        $query->whereHas('options', function ($query) use ($children) {
            $this->processFilter($children->first(), $query);
        });
        $children->shift();
        $operator = $this->getEloquentOperator($group);    
        foreach ($children as $childFilter) {
            $query->whereHas('options', function ($query) use ($childFilter, $operator) {
                $this->processFilter($childFilter, $query, $operator);
            });
        }
    }

    protected function processFilter($filter, $query, $operator = "where")
    {
        if ($filter->type == "query-builder-rule") {
            $query->$operator('options.id', '=', $filter->query->value);
        } else if ($filter->type == "query-builder-group") {
            $query->$operator(function ($subQuery) use ($filter) {
                $this->buildForFilterGroup($subQuery, $filter->query);
            });
        }
    }

    protected function getEloquentOperator($group)
    {
        if ($group->logicalOperator == "any")
            return "orWhere";
        else if ($group->logicalOperator == "all")
            return "where";
    }
    
}
