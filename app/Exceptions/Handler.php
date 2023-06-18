<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\URL;
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {

        set_time_limit(0);
        // return parent::render($request, $exception);
        if ($exception instanceof \Illuminate\Session\TokenMismatchException){ // <<<=========== the Code
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }
            // return  Redirect::back()->with('message','You page session expired. Please try again !')
            return redirect(URL::current())->with('message', 'Your token has been expired. Please try again');
        }
        return parent::render($request, $exception);
    }

}
