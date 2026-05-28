<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Failed Validation
        $exceptions->renderable(function (ValidationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Validation Failed',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        // Route Model Binding Not Found
        $exceptions->renderable(function (ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Data Not Found',
                ], 404);
            }
        });

        // Route Not Found
        $exceptions->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Data Not Found',
                ], 404);
            }
        });

        // Other HTTP Errors
        $exceptions->renderable(function (HttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $code = $e->getStatusCode();

                return response()->json([
                    'status' => $code,
                    'message' => $e->getMessage() ?: 'HTTP Error',
                ], $code);
            }
        });

        // Fallback 500 - Unexpected Errors
        $exceptions->renderable(function (Throwable $th, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'status' => 500,
                    'message' => app()->hasDebugModeEnabled() ? ($th->getMessage() ?: 'Internal Server Error') : 'Internal Server Error',
                ], 500);
            }
        });
    })->create();
