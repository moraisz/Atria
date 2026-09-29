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
        'mysql' => [
            'driver' => 'mysql',
            'host' => EnvHelper::env('DB_HOST', 'localhost'),
            'port' => EnvHelper::env('DB_PORT', 3306),
            'database' => EnvHelper::env('DB_DATABASE', 'myapp'),
            'username' => EnvHelper::env('DB_USERNAME', 'user'),
            'password' => EnvHelper::env('DB_PASSWORD', 'password'),
            'charset' => EnvHelper::env('DB_CHARSET', 'utf8mb4'),
            'max_lifetime' => EnvHelper::env('DB_MAX_LIFETIME', 0),
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
            'max_lifetime' => EnvHelper::env('DB_MAX_LIFETIME', 0),
        ],
    ],
    'migrations_paths' => [__DIR__ . '/../app/Migrations'],
];
