<?php

namespace App\Http\Middleware;

use Closure;
use App\Ideation;

class CheckIfIdeationExists
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->ideation)
        {
            $ideation = Ideation::where('slug', $request->ideation)->whereNull('deleted_at')->first();

            if(!$ideation)
                return redirect('/ideations')->withErrors(['msg', 'Oops! This ideation doesn\'t exist.']);
            else
            {
                if($ideation->participants()->where('user_id', $request->user()->id)->count() || $ideation->invitations()->where('user_id', $request->user()->id)->count() || $request->user()->is_admin || $request->user()->is_group_admin)
                    return $next($request);
                else
                    return redirect('/ideations')->withErrors(['msg', 'Oops! You haven\'t been invited to that ideation.']);
            }
        }

        return $next($request);
    }
}
