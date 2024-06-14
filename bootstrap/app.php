<?php


use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Exceptions\ApplicationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        // health: '/up',
        using: function () {
            Route::middleware('api')
                ->prefix('api/v1')
                ->group(base_path('routes/v1/api.php'));
     
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (ApplicationException $e) {
            if (request()->is('api/*')) {
                Log::error(request()->fullUrl(), [$e->getMessage()]);
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], $e->getStatusCode());
            }
        });
        $exceptions->renderable(function (AuthenticationException $e) {
            if (request()->is('api/*')) {
                Log::error(request()->fullUrl(), [$e->getMessage()]);
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 401);
            }
        });
        $exceptions->renderable(function (NotFoundHttpException $e) {
            if (request()->is('api/*')) {
                Log::error(request()->fullUrl(), [$e->getMessage()]);
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], $e->getStatusCode());
            }

        });
        $exceptions->renderable(function (UnauthorizedHttpException $e) {
            if (request()->is('api/*')) {
                Log::error(request()->fullUrl(), [$e->getMessage()]);
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], $e->getStatusCode());
            }
        });
        $exceptions->renderable(function (MethodNotAllowedHttpException $e) {
            if (request()->is('api/*')) {
                Log::error(request()->fullUrl(), [$e->getMessage()]);
                return response()->json([
                    'success' => false,
                    'message' => 'The given HTTP method is not supported for the given route',
                ], 405);
            }
        });
        $exceptions->renderable(function (DecryptException $e) {
            if (request()->is('api/*')) {
                Log::error(request()->fullUrl(), [$e]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid URL',
                ], 404);
            }
        });
        $exceptions->renderable(function (FileNotFoundException $e) {
            if (request()->is('api/*')) {
                Log::error(request()->fullUrl(), [$e]);
                return response()->json([
                    'success' => false,
                    'message' => 'File Not Found',
                ], 404);
            }
        });
        $exceptions->renderable(function (ThrottleRequestsException $e) {
            if (request()->is('api/*')) {
                Log::error(request()->fullUrl(), [$e]);
                return response()->json([
                    'success' => false,
                    'message' => 'Too many request, please slow down.',
                ], 429);
            }
        });
        $exceptions->renderable(function (Exception $e) {
            if (request()->is('api/*')) {
                Log::error(request()->fullUrl(), [$e]);
                return response()->json([
                    'success' => false,
                    'message' => 'Something happening in server please contact the admin',
                ], 500);
            }
        });
    })->create();
