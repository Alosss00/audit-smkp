<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        // Gracefully handle CSRF token expiration (419 Page Expired)
        if ($e instanceof \Illuminate\Session\TokenMismatchException || ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException && $e->getStatusCode() === 419)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sesi Anda telah kedaluwarsa. Silakan refresh halaman dan coba kembali.',
                ], 419);
            }

            return redirect()->route('login')
                ->with('error', 'Sesi Anda telah kedaluwarsa atau halaman telah expired. Silakan coba login kembali.');
        }

        return parent::render($request, $e);
    }
}
