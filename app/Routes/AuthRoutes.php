<?php

declare(strict_types=1);

namespace App\Routes;

use Atria\Http\Contracts\Routes;
use Atria\Modules\Auth\Middlewares\GuestMiddleware;
use Atria\Http\Router;
use Atria\Modules\Csrf\Middlewares\CsrfMiddleware;
use App\Controllers\AuthController;

class AuthRoutes implements Routes
{
    public static function register(Router $router): void
    {
        $router->get('/login', [AuthController::class, 'login'], [GuestMiddleware::class]);
        $router->post('/login', [AuthController::class, 'loginUser'], [CsrfMiddleware::class]);
        $router->get('/register', [AuthController::class, 'register'], [GuestMiddleware::class]);
        $router->post('/register', [AuthController::class, 'registerUser'], [CsrfMiddleware::class]);
        $router->post('/refresh', [AuthController::class, 'refresh']);
        $router->post('/logout', [AuthController::class, 'logout'], [CsrfMiddleware::class]);
    }
}
