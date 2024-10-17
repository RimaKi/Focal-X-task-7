<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
        $this->renderable(function (NotFoundHttpException|Throwable $e) {
            $message = null;
            $code = null;

            if ($e instanceof NotFoundHttpException) {
                Log::error('Not found http exception :' . $e->getMessage());
                $message = 'Not Found.';
                $code = 404;
            }
            if ($e instanceof AuthenticationException) {
                Log::error('Authentication Exception :' . $e->getMessage());
                $message = "trans.unauthenticated";
                $code = 401;
            }
            if ($e instanceof AuthorizationException) {
                Log::error('Authorization Exception :' . $e->getMessage());
                $message = "Access Denied!!";
                $code = 403;
            }
            if ($e instanceof UnauthorizedException) {
                Log::error('Unauthorized Exception :' . $e->getMessage());
                $message = "Access Denied!!";
                $code = 403;
            }
            if ($e instanceof ValidationException) {
                Log::error('Validation Exception :' . $e->getMessage());
                $message = $e->errors();
            }
            if ($e instanceof ModelNotFoundException) {
                Log::error('Model Not Found Exception :' . $e->getMessage());
                $message = 'Model not fount';
            }
            $extra = [];
            if (env("APP_DEBUG", false)) {
                $extra = [
                    "details" => $e->getTrace()
                ];
            }
            return response()->json([
                'success' => false,
                'message' => $message != null ? $message : $e->getMessage(),
                ...$extra
            ], $code != null ? $code : ($e->getCode() >= 100 && $e->getCode() < 600 ? $e->getCode() : 402));
        });
    }
}
