<?php

namespace App\Listeners\Loggers\Budgets;

use App\Events\Budgets\ExpenseUpdated;

class LogExpenseUpdated
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\OrderShipped  $event
     * @return void
     */
    public function handle(ExpenseUpdated $event)
    {
        $event->user->logs()->create([
            'action'             => 'updated expense',
            'related_model_type' => get_class($event->budget),
            'related_model_id'   => $event->budget->id,
            'message' => '<a href="/groups/' . $event->group->slug . '">' . 'group: ' . $event->group->name . '</a>',
        ]);
    }
}