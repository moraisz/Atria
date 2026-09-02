<?php

declare(strict_types=1);

use Atria\Helpers\EnvHelper;

return [
    'default' => EnvHelper::env('DB_CONNECTION', 'sqlite'),
    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'database' => EnvHelper::env('DB_DATABASE', __DIR__ . '/../database/database.sqlite'),
            'prefix' => '',
        ],
        'pgsql' => [
            'driver' => 'pgsql',
            'host' => EnvHelper::env('DB_HOST', 'localhost'),
            'port' => EnvHelper::env('DB_PORT', 5432),
            'database' => EnvHelper::env('DB_DATABASE', 'myapp'),
            'username' => EnvHelper::env('DB_USERNAME', 'user'),
            'password' => EnvHelper::env('DB_PASSWORD', 'password'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ],
    ],
    'migrations_paths' => [__DIR__ . '/../app/Migrations'],
];
