<?php

use App\Exceptions\DomainException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // HandleDomainException
        $exceptions->report(function (DomainException $e) {
            Log::warning("Business error: {$e->getErrorCode()} - {$e->getMessage()}", [
                'exception' => $e,
                'errorCode' => $e->getErrorCode()
            ]);
        })->stop(); // Предотвращает дублирование в основной лог

        // HandleException
        $exceptions->report(function (Throwable $e) {
            Log::error("Unhandled server error: " . $e->getMessage(), [
                'exception' => $e
            ]);
        });

        // WriteProblemDetails
        $exceptions->render(function (Throwable $e, Request $request) {

            // Если это доменное исключение
            if ($e instanceof DomainException) {
                return response()->json([
                    'status' => $e->getCode(), // HTTP статус (400)
                    'title' => 'Business Error',
                    'detail' => $e->getMessage(),
                    'extensions' => [
                        'StatusCode' => $e->getErrorCode() // ErrorCode
                    ]
                ], $e->getCode());
            }

            // Internal Server Error
            return response()->json([
                'status' => 500,
                'title' => 'Internal Server Error',
                'detail' => config('app.debug') ? $e->getMessage() : 'An unexpected error occurred.'
            ], 500);
        });
    })->create();
