<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * * @return void
     * *
     * * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     *                                             * @return \Symfony\Component\HttpFoundation\Response
     *                                             *
     *                                             * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof AuthorizationException && $request->expectsJson()) {
            return response()->json(['errors' => [
                'message' => $exception->getMessage()]], Response::HTTP_FORBIDDEN);

        }
        if ($exception instanceof ModelNotFoundException && $request->expectsJson()) {
            return response()->json(['errors' => [
                'message' => 'The resource was not found']], Response::HTTP_NOT_FOUND);
        }

        if ($exception instanceof ModelNotDefined && $request->expectsJson()) {
            return response()->json(['errors' => [
                'message' => 'No model defined']], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return parent::render($request, $exception);
    }
}
