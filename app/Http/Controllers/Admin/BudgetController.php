<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\Event;
use App\Group;
use App\Budget;
use App\Expense;
use Carbon\Carbon;
use App\ExpenseCategory;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Exports\BudgetsExport;
use App\Exports\ExpensesExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BudgetController extends Controller
{
    public function index()
    {
        $budgets = Budget::orderBy('quarter', 'desc')->get()->sortByDesc('year');
        $spendByGroup = DB::table('budgets')->selectRaw('groups.name as groupName, sum(amount) as spent, sum(budgets.total_budget) as budgetTotal')->leftJoin('groups', 'groups.id', '=', 'budgets.group_id')->leftJoin('expenses', 'expenses.budget_id', '=', 'budgets.id')->whereRaw('budgets.year = YEAR(CURDATE())')->groupBy('groupName')->get();
        $spendByGroup = $spendByGroup->map(function ($item) {
            $item->spent = $item->spent/100;
            $item->budgetTotal = $item->budgetTotal/100;
            return $item;
        });

        // $budgetBreakdown = DB::table('budgets')->select('groups.name', 'budgets.quarter', 'total_budget')->join('groups', 'budgets.group_id', '=', 'groups.id')->get();
        $budgetBreakdown = Group::with('budgets')->whereHas('budgets', function ($query) {
            $query->whereRaw('budgets.year = YEAR(CURDATE())');
        })->get();

        foreach ($budgetBreakdown as $group) {
            $group->quarters = [
                ($group->budgets->where('quarter', '1')->first()) ? $group->budgets->where('quarter', '1')->first()->total_budget/100 : 0,
                ($group->budgets->where('quarter', '2')->first()) ? $group->budgets->where('quarter', '2')->first()->total_budget/100 : 0,
                ($group->budgets->where('quarter', '3')->first()) ? $group->budgets->where('quarter', '3')->first()->total_budget/100 : 0,
                ($group->budgets->where('quarter', '4')->first()) ? $group->budgets->where('quarter', '4')->first()->total_budget/100 : 0,
            ];
            $group->spentQuarters = [
                ($group->budgets->where('quarter', '1')->first()) ? $group->budgets->where('quarter', '1')->first()->spent/100 : 0,
                ($group->budgets->where('quarter', '2')->first()) ? $group->budgets->where('quarter', '2')->first()->spent/100 : 0,
                ($group->budgets->where('quarter', '3')->first()) ? $group->budgets->where('quarter', '3')->first()->spent/100 : 0,
                ($group->budgets->where('quarter', '4')->first()) ? $group->budgets->where('quarter', '4')->first()->spent/100 : 0,
            ];
        }

        $colors = ["rgb(255, 137, 125)","rgb(52, 84, 117)","rgb(152, 114, 132)","rgb(255, 195, 189)","rgb(102, 133, 165)","rgb(135, 193, 143)","rgb(83, 135, 90)","rgb(229, 190, 209)","rgb(93, 187, 191)","rgb(255, 232, 229)","rgb(103, 168, 235)","rgb(138, 239, 152)"];

        return view('admin.budgets.index')->with([
            'budgets' => $budgets,
            'spendByGroup' => $spendByGroup,
            'budgetBreakdown' => $budgetBreakdown,
            'colors' => $colors,
        ]);
    }

    public function create(Request $request)
    {
        return view('admin.budgets.create')->with([
            'groups' => Group::orderBy('name', 'asc')->get(),
            'group' => $request->input('group'),
        ]);
    }

    public function store(Request $request)
    {
         $messages = array(
            'total_budget.digits_between'=>'Amount must be below $10,000,000',
        );

        $rules = array(
            'total_budget'=>'required|digits_between:1,7',
        );

        $validator = Validator::make($request->all(), $rules, $messages);

        if($validator->fails())
            return redirect()->back()->withErrors($validator)->withInput($request->all());

        $existingBudget = Budget::where('quarter', '=', $request->quarter)
                                ->where('group_id', '=', $request->group_id)
                                ->where('year', '=', $request->year)
                                ->first();
        if ($existingBudget)
            return redirect()->back()->withErrors(['total_budget' => 'There is already a budget for that group for that quarter.']);

        $cents = 100 * parse_money($request->total_budget);

        $budget = Budget::create([
            'year' => $request->year,
            'quarter' => $request->quarter,
            'group_id' => $request->group_id,
            'total_budget' => $cents,
        ]);

        return redirect('/admin/budgets/' . $budget->id);
    }

    public function show($id, Request $request)
    {
        $budget = Budget::find($id);

        $spendByCategory = DB::table('expenses')->selectRaw("name, sum(amount) as total")->groupBy('expense_category_id')->where('budget_id', '=', $id)->leftJoin('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')->get();
        if ($spendByCategory->where('name', null)->first())
            $spendByCategory->where('name', null)->first()->name = 'Uncategorized';
        $spendByCategory = $spendByCategory->map(function ($category) {
            $category->total = $category->total/100;
            return $category;
        });
        $weekData = DB::table('expenses')->selectRaw('week(date) as week, sum(amount) as total')->groupBy('week')->where('budget_id', '=', $id)->get();
        $spendOverTime = collect();
        $totalSpend = 0;
        if($weekData && $weekData->first()) {
            for ($i = $weekData->first()->week; $i <= $weekData->last()->week; $i++) {
                $totalSpend += $weekData->where('week', $i)->sum('total');
                $spendOverTime->push([
                    'week' => Carbon::now()->setISODate($budget->year,$i)->startOfWeek()->format('M d, Y'),
                    'amount' => $totalSpend/100
                ]);
            }
        }

        return view('admin.budgets.show')->with([
            'budget' => $budget,
            'spendByGroup' => $spendByCategory,
            'spendOverTime' => $spendOverTime,
        ]);
    }

    public function edit($id, Request $request)
    {
        return view('admin.budgets.edit')->with([
            'budget' => Budget::find($id),
            'groups' => Group::orderBy('name', 'asc')->get(),
        ]);
    }

    public function delete($id, Request $request)
    {
        $budget = Budget::find($id);
        $budget->expenses()->delete();
        $budget->delete();

        return redirect('/admin/budgets');
    }

    public function update($id, Request $request)
    {
        $cents = 100 * parse_money($request->total_budget);

        $budget = Budget::where('id', '=', $id)
                        ->update([
                            'year' => $request->year,
                            'quarter' => $request->quarter,
                            'group_id' => $request->group_id,
                            'total_budget' => $cents,
                        ]);

        return redirect('/admin/budgets/' . $id);
    }

    public function addExpense($id, Request $request)
    {
        $budget = Budget::find($id);
        $month = 3*$budget->quarter-2;
        $placeholderDate = $month . "/01/" . $budget->year;
        $events = Event::whereBetween('date', [Carbon::parse($placeholderDate)->subMonths(6)->toDateTimeString(), Carbon::parse($placeholderDate)->addMonths(6)->toDateTimeString()])->get();

        return view('admin.budgets.expenses.create')->with([
            'budget' => $budget,
            'placeholderDate' => $placeholderDate,
            'categories' => ExpenseCategory::orderBy('name', 'asc')->get(),
            'events' => $events,
        ]);
    }

    public function postExpense($id, Request $request)
    {
        $budget = Budget::find($id);

        $cents = 100 * parse_money($request->amount);

        Expense::create([
            'date' => Carbon::parse($request->input('date'))->toDateString(),
            'amount' => $cents,
            'description' => $request->description,
            'budget_id' => $id,
            'user_id' => $request->user()->id,
            'expense_category_id' => ($request->category_id != "null") ? $request->category_id : null,
            'event_id' => ($request->event_id != "null" && $request->isForEvent == 'yes') ? $request->event_id : null,
        ]);

        return redirect('/admin/budgets/' . $id);
    }

    public function editExpense($budget, $expense, Request $request)
    {
        $budget = Budget::find($budget);
        $expense = Expense::find($expense);

        $month = 3*$budget->quarter-2;
        $placeholderDate = $month . "/01/" . $budget->year;
        $events = Event::whereBetween('date', [Carbon::parse($placeholderDate)->subMonths(6)->toDateTimeString(), Carbon::parse($placeholderDate)->addMonths(6)->toDateTimeString()])->get();

        return view('admin.budgets.expenses.edit')->with([
            'budget' => $budget,
            'expense' => $expense,
            'placeholderDate' => $placeholderDate,
            'categories' => ExpenseCategory::orderBy('name', 'asc')->get(),
            'events' => $events,
        ]);
    }

    public function updateExpense($budget, $expense, Request $request)
    {
        $cents = 100 * parse_money($request->amount);

        Expense::find($expense)->update([
            'date' => Carbon::parse($request->input('date'))->toDateString(),
            'amount' => $cents,
            'description' => $request->description,
            'expense_category_id' => ($request->category_id != "null") ? $request->category_id : null,
            'event_id' => ($request->event_id != "null" && $request->isForEvent == 'yes') ? $request->event_id : null,
        ]);

        return redirect('/admin/budgets/' . $budget);
    }

    public function deleteExpense($budget, $expense)
    {
        $expense = Expense::find($expense);
        $expense->delete();

        return redirect('/admin/budgets/' . $budget);
    }

    public function export(Excel $excel)
    {
        return $excel->download(new BudgetsExport, 'budgets.xlsx');
    }

    public function exportExpenses($id, Excel $excel)
    {
        $budget = Budget::find($id);
        return $excel->download(new ExpensesExport($budget), $budget->year.' Q'.$budget->quarter.' - '.((optional(optional($budget)->group)->name) ?: 'DELETED').' - expenses.xlsx');
    }
}
