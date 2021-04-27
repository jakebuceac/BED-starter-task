<?php

namespace App\Exceptions;

use ArgumentCountError;
use HttpException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Throwable;

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
     * Render the exception handling callbacks for the application.
     *
     * @param $request
     * @param Throwable $e
     * @return JsonResponse
     */
    public function render($request, Throwable $e)
    {
        if($e instanceof HttpException) {

            return new JsonResponse(['error' => $e->getMessage()], $e->getStatusCode());

        } else if($e instanceof AuthenticationException) {

            return new JsonResponse((object) [], 401);

        } else if($e instanceof AuthorizationException) {

            return new JsonResponse((object) [], 401);

        } else if($e instanceof ValidationException) {

            return new JsonResponse($e->errors(), 405);

        } else if ($e instanceof ModelNotFoundException) {

            return new JsonResponse(['error' => ['The entity could not be found.']], 404);
        }

        return new JsonResponse([
            'data' => [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]
        ], 500);
    }
}
