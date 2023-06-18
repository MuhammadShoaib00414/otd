<?php

namespace App\Events\Budgets;

use App\User;
use App\Group;
use App\Budget;
use App\Expense;
use Illuminate\Queue\SerializesModels;

class ExpenseSaved
{
    use SerializesModels;

    public $user;
    public $budget;
    public $group;
    public $expense;

    /**
     * Create a new event instance.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function __construct(User $user, Budget $budget, Group $group, Expense $expense)
    {
        $this->user = $user;
        $this->budget = $budget;
        $this->group = $group;
        $this->expense = $expense;
    }
}