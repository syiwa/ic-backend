<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;

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
        if ($exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {
            return jsonResponse([
                "message" => "Unauthorized."
            ],403);
        }
        
        return parent::render($request, $exception);
    }

    /**
     * Moving invalid request to custom responses in purpose to keep status code 200.
     * @param  [type]              $request   [description]
     * @param  ValidationException $exception [description]
     * @return [type]                         [description]
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        return jsonResponse($exception->errors(), $exception->status);
    }

    /**
     * Unauthenticated default redirect.
     * @param  [type]                  $request   [description]
     * @param  AuthenticationException $exception [description]
     * @return [type]                             [description]
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return ($request->expectsJson()) ? 
                jsonResponse(['message' => 'Unauthenticated.'], 401)
                : route('login');
    }
}
