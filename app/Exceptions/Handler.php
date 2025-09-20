<?php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException; 
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Configuration\Exceptions;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class Handler 
{
    use ApiResponse;
  
   
    public function handleApiException(Exceptions $exceptions)
    {

        $exceptions->renderable(function (AccessDeniedHttpException $e, Request $request) {
            return $this->error($e->getMessage(), ResponseAlias::HTTP_FORBIDDEN);
        });
        $exceptions->renderable(function (ValidationException $e, Request $request) {
            return $this->error($e->getMessage(), ResponseAlias::HTTP_FORBIDDEN);
        });
        $exceptions->renderable(function (AuthenticationException $e, Request $request) {
            return $this->error($e->getMessage(), ResponseAlias::HTTP_FORBIDDEN);
        });
        $exceptions->renderable(function (AuthorizationException $e, Request $request) {
            return $this->error($e->getMessage(), ResponseAlias::HTTP_FORBIDDEN);
        });
        $exceptions->renderable(function (ModelNotFoundException|NotFoundHttpException $e , Request $request) {
            return $this->error($e->getMessage(), ResponseAlias::HTTP_FORBIDDEN);
        });

       
   
         $exceptions->renderable(function (Throwable $e, Request $request) {
            if (app()->isProduction()) {
                return $this->error(
                    message: 'Internal Server Error',
                    statusCode: ResponseAlias::HTTP_INTERNAL_SERVER_ERROR
                );
            }
        });
    }
}
