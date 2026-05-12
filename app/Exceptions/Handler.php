<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        RouterConnectionLostException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof RouterConnectionLostException) {
            $message = 'Connection to the router was lost while processing your request. '
                     . 'The operation was not completed. Please try again.';

            if ($request->ajax() || $request->wantsJson()) {
                return response(
                    "<p class='text-danger'><i class='fas fa-exclamation-triangle'></i> " . $message . "</p>",
                    200
                );
            }

            session()->flash('error_router', $message);
            return redirect()->back();
        }

        return parent::render($request, $exception);
    }
}
