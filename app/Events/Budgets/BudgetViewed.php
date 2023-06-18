<?php

namespace App\Events\Budgets;

use App\User;
use App\Group;
use App\Budget;
use Illuminate\Queue\SerializesModels;

class BudgetViewed
{
    use SerializesModels;

    public $user;
    public $budget;
    public $group;

    /**
     * Create a new event instance.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function __construct(User $user, Budget $budget, Group $group)
    {
        $this->user = $user;
        $this->budget = $budget;
        $this->group = $group;
    }
}