<?php

use App\Http\Middleware\ReturnJsonResponseMiddleware;
use App\Http\Middleware\IdentifyTenant;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        api: __DIR__ . '/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(ReturnJsonResponseMiddleware::class);
        $middleware->append(\App\Http\Middleware\IdentifyTenant::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->reportable(function (\Throwable $e) {
            try {
                $auditService = app(\App\Domains\Auditoria\Services\AuditService::class);
                $auditService->log('server_error_500', 'exception', null, null, null, [
                    'exception' => get_class($e),
                    'message' => \Illuminate\Support\Str::limit($e->getMessage(), 500),
                    'file' => $e->getFile() . ':' . $e->getLine(),
                ]);
            } catch (\Throwable) {
                // Avoid recursive failures
            }
        });
    })->create();
