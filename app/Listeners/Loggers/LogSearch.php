<?php

namespace App\Listeners\Loggers;

use App\Events\SearchEvent;

class LogSearch
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
    public function handle(SearchEvent $event)
    {
        $event->user->logs()->create([
            'action'  => 'search',
            'message' => 'Search term: ' . $event->searchTerm,
        ]);
    }
}