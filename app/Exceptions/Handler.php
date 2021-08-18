<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Throwable;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

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
  protected $dontFlash = ['password', 'password_confirmation'];

  /**
   * Report or log an exception.
   *
   * @param  \Throwable  $exception
   * @return void
   *
   * @throws \Exception
   */
  public function report(Throwable $exception)
  {
    parent::report($exception);
  }

  /**
   * Render an exception into an HTTP response.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Throwable  $exception
   * @return \Symfony\Component\HttpFoundation\Response
   *
   * @throws \Throwable
   */
  public function render($request, Throwable $exception)
  {
    // if($exception){
    //     return response()->json(['error' => 'Unexpected error. Please try again.'], $exception->getStatusCode());
    //     // return response()->json(['error' =>  $exception->getMessage()], $exception->getStatusCode());
    // }

    // if ($exception instanceof ValidationException) {
    //   return response()->json(
    //     ['error' => $exception->validator->errors()->getMessages()],
    //     400
    //   );
    // } elseif ($exception instanceof JWTException) {
    //   return response()->json(['tokenError' => 'token error'], 401);
    // } elseif ($exception instanceof AuthenticationException) {
    //   return response()->json(['tokenError' => 'token error'], 401);
    // } elseif ($exception) {
    //   return response()->json(
    //     ['error' => 'Unexpected error. Please try again.'],
    //     $exception->getStatusCode()
    //   );
    // } else {
    //   return response()->json(
    //     ['error' => 'Unexpected error. Please try again.'],
    //     $exception->getStatusCode()
    //   );
    // }
    return parent::render($request, $exception);
  }
}