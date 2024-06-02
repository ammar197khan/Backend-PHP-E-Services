<?php

namespace App\Exceptions;

use Exception;
use Request;
use Auth;
use Log;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
    public function report(Exception $exception)
    {
        parent::report($exception);

        if($this->shouldReport($exception)){
            Log::info(null, [
              'verb'    => Request::method(),
              'url'     => Request::fullUrl(),
              'body'    => Request::all(),
              'admins'  => [
                  'qreeb'    => Auth::guard('admin')->id(),
                  'provider' => Auth::guard('provider')->id(),
                  'company'  => Auth::guard('company')->id(),
              ]
              // 'headers' => Request::header(),
            ]);
        }
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // return redirect()->back();
        return parent::render($request, $exception);
    }
}
