<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Http\Request;

class AccountComposer
{
    /**
     * The user repository implementation.
     *
     * @var UserRepository
     */
    protected $user;
    protected $organization;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function compose(View $view)
    {
        if ($this->request->user()) {
            $view->with('authUser', $this->request->user())
                 ->with('agent', (new \Jenssegers\Agent\Agent));
        }
    }
}