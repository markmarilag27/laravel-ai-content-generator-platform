<?php

use App\Http\Middleware\ElevateRlsMiddleware;
use App\Http\Middleware\WorkspaceScopeMiddleware;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Database\QueryException as DatabaseQueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            ElevateRlsMiddleware::class,
            WorkspaceScopeMiddleware::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);

        $middleware->priority([
            StartSession::class,
            ElevateRlsMiddleware::class,
            Authenticate::class,
            WorkspaceScopeMiddleware::class,
            SubstituteBindings::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (DatabaseQueryException $e, Request $request) {
            // 42501 is the Postgres error code for RLS violations
            if ($e->getCode() === '42501') {
                throw new AccessDeniedHttpException('Tenant isolation violation.');
            }
        });
    })->create();
