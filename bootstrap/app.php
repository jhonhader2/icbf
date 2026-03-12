<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        assert($middleware instanceof Middleware);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->reportable(function (\Throwable $e): void {
            $logFile = storage_path('logs/import-errors.log');
            $line = date('Y-m-d H:i:s') . ' ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() . "\n" . $e->getTraceAsString() . "\n\n";
            @file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
        });

        $exceptions->renderable(function (ModelNotFoundException $e, Request $request) {
            if (! $request->expectsJson()) {
                $model = $e->getModel();
                if ($model === \App\Models\Persona::class) {
                    return redirect()->route('personas.index')->with('error', __('Persona no encontrada.'));
                }
            }
        });
    })->create();
