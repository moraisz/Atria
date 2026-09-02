<?php

declare(strict_types=1);

namespace App\Routes;

use Atria\Http\Contracts\Routes;
use Atria\Modules\Auth\Middlewares\AuthMiddleware;
use Atria\Http\Router;
use Atria\Modules\Csrf\Middlewares\CsrfMiddleware;
use App\Controllers\HomeController;

class HomeRoutes implements Routes
{
    public static function register(Router $router): void
    {
        $router->get('/', [HomeController::class, 'get'], [AuthMiddleware::class]);
        $router->post('/mercure/publish', [HomeController::class, 'publishMercure'], [AuthMiddleware::class, CsrfMiddleware::class]);
    }
}
