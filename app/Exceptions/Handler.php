<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException; 
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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        if ($request->is('api/*')) {
            return $this->handleApiException($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * Handle API exceptions with consistent JSON responses.
     */
    protected function handleApiException($request, Throwable $e)
    {
        if ($e instanceof ValidationException) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        if ($e instanceof AuthenticationException) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        if ($e instanceof AuthorizationException) {
            return response()->json([
                'status' => 'error',
                'message' =>  $e->getMessage() ?: 'You are not authorized to access this resource'
            ], 403);
        }
    

        if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
            return response()->json([
                'message' => 'Resource not found'
            ], 404);
        }

        return response()->json([
            'message' => $this->isHttpException($e) ? $e->getMessage() : 'Internal server error'
        ], $this->isHttpException($e) ? $e->getStatusCode() : 500);
    }
}
