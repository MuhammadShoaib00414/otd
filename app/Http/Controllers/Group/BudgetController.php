<?php

namespace App\Http\Controllers\Group;

use App\Event;
use App\Group;
use App\Budget;
use App\Expense;
use Carbon\Carbon;
use App\ExpenseCategory;
use Illuminate\Http\Request;
use App\Events\Budgets\BudgetViewed;
use App\Events\Budgets\ExpenseSaved;
use App\Http\Controllers\Controller;
use App\Events\Budgets\ExpenseDeleted;
use App\Events\Budgets\ExpenseUpdated;
use Illuminate\Support\Facades\Storage;

class BudgetController extends Controller
{

    public function __construct()
    {
        $this->middleware(['groupadmin', 'group']);
    }

    public function index($slug, Request $request)
    {
        $group = Group::where('slug', '=', $slug)->first();

        return view('groups.budgets.index')->with([
            'group' => $group,
        ]);
    }

    public function show($slug, $id, Request $request)
    {
        $group = Group::where('slug', '=', $slug)->first();
        $budget = Budget::find($id);
        if ($budget->group_id != $group->id)
            return 'Error: Unauthorized access of budget';

        event(new BudgetViewed($request->user(), $budget, $group));

        return view('groups.budgets.show')->with([
            'group' => $group,
            'budget' => $budget,
        ]);
    }

    public function addExpense($slug, $id, Request $request)
    {
        $group = Group::where('slug', '=', $slug)->first();
        $budget = Budget::find($id);
        if ($budget->group_id != $group->id)
            return 'Error: Unauthorized access of budget';

        $month = 3*$budget->quarter-2;
        $placeholderDate = $month . "/01/" . $budget->year;
        $events = Event::whereBetween('date', [Carbon::parse($placeholderDate)->subMonths(6)->toDateTimeString(), Carbon::parse($placeholderDate)->addMonths(6)->toDateTimeString()])->get();

        return view('groups.budgets.expenses.create')->with([
            'group' => $group,
            'budget' => $budget,
            'placeholderDate' => $placeholderDate,
            'categories' => ExpenseCategory::orderBy('name', 'asc')->get(),
            'events' => $events,
        ]);
    }

    public function saveExpense($slug, $id, Request $request)
    {
        $validate = $request->validate([
            'date' => 'required',
            'amount' => 'required',
            'receipt' => 'file|max:51200',
        ]);

        $group = Group::where('slug', '=', $slug)->first();
        $budget = Budget::find($id);
        if ($budget->group_id != $group->id)
            return 'Error: Unauthorized access of budget';

        $cents = 100 * parse_money($request->amount);

        $expense = Expense::create([
            'date' => Carbon::parse($request->date)->toDateString(),
            'amount' => $cents,
            'description' => $request->description,
            'budget_id' => $id,
            'user_id' => $request->user()->id,
            'expense_category_id' => ($request->category_id != "null") ? $request->category_id : null,
            'event_id' => ($request->event_id != "null" && $request->isForEvent == 'yes') ? $request->event_id : null,
        ]);

        if ($request->has('receipt')) {
            $expense->receipt_file_name = $request->file('receipt')->getClientOriginalName();
            $expense->receipt_file_path = $request->file('receipt')->store('receipts/'.$slug, 's3');
            $expense->save();
        }

        event(new ExpenseSaved($request->user(), $budget, $group, $expense));

        return redirect('/groups/' . $slug . '/budgets/' . $id);
    }

    public function editExpense($slug, $budget, $expense, Request $request)
    {
        $group = Group::where('slug', '=', $slug)->first();
        $budget = Budget::find($budget);
        $expense = Expense::find($expense);

        $month = 3*$budget->quarter-2;
        $placeholderDate = $month . "/01/" . $budget->year;
        $events = Event::whereBetween('date', [Carbon::parse($placeholderDate)->subMonths(6)->toDateTimeString(), Carbon::parse($placeholderDate)->addMonths(6)->toDateTimeString()])->get();

        return view('groups.budgets.expenses.edit')->with([
            'group' => $group,
            'budget' => $budget,
            'expense' => $expense,
            'placeholderDate' => $placeholderDate,
            'categories' => ExpenseCategory::orderBy('name', 'asc')->get(),
            'events' => $events,
        ]);
    }

    public function updateExpense($slug, $budget, $id, Request $request)
    {
        $validate = $request->validate([
            'date' => 'required',
            'amount' => 'required',
            'receipt' => 'file|max:51200',
        ]);
        
        $expense = Expense::find($id);
        $cents = 100 * parse_money($request->amount);

        $expense->update([
            'date' => Carbon::parse($request->date)->toDateString(),
            'amount' => $cents,
            'description' => $request->description,
            'expense_category_id' => ($request->category_id != "null") ? $request->category_id : null,
            'event_id' => ($request->event_id != "null" && $request->isForEvent == 'yes') ? $request->event_id : null,
        ]);

        if ($request->has('receipt')) {
            $expense->receipt_file_name = $request->file('receipt')->getClientOriginalName();
            $expense->receipt_file_path = $request->file('receipt')->store('receipts/'.$slug);
            $expense->save();
        }

        $group = Group::where('slug', '=', $slug)->first();

        $fullBudget = Budget::find($budget);

        event(new ExpenseUpdated($request->user(), $fullBudget, $group, $expense));

        return redirect('/groups/' . $slug . '/budgets/' . $budget);
    }

    public function deleteExpense($slug, $budget, $id, Request $request)
    {
        $expense = Expense::find($id);
        $expense->delete();

        $fullBudget = Budget::find($budget);

        $group = Group::where('slug', '=', $slug)->first();

        event(new ExpenseDeleted($request->user(), $fullBudget, $group, $expense));

        return redirect('/groups/' . $slug . '/budgets/' . $budget);
    }

    public function downloadExpenseReceipt($slug, $budgetId, $expenseId)
    {
        $expense = Expense::find($expenseId);

        $headers = getS3DownloadHeaders($expense->receipt_file_path, $expense->receipt_file_name);

        return \Response::make(Storage::disk('s3')->get($expense->receipt_file_path), 200, $headers);
    }
}
