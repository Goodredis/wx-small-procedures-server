<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Dingo\Api\Exception\Handler as DingoHandler;

class ApiHandler extends DingoHandler
{
    public function handle(Exception $exception)
    {
        if ($exception instanceof NotFoundHttpException) {
            return response()->json(['message' => 'the requested resource was not found', 'status_code' => 404], 404);
        }
        return parent::handle($exception);
    }
}
