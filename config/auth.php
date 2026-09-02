<?php

declare(strict_types=1);

use Atria\Helpers\EnvHelper;

return [
    'driver' => EnvHelper::env('AUTH_DRIVER', 'standard'),
    'secret' => EnvHelper::env('AUTH_SECRET', EnvHelper::env('APP_KEY', '')),
    'access_ttl' => max(1, (int) EnvHelper::env('AUTH_ACCESS_TTL', 300)),
    'refresh_ttl' => max(1, (int) EnvHelper::env('AUTH_REFRESH_TTL', 86400)),
    'redirect_guest_to' => EnvHelper::env('AUTH_REDIRECT_GUEST_TO', '/login'),
    'redirect_authenticated_to' => EnvHelper::env('AUTH_REDIRECT_AUTHENTICATED_TO', '/'),
    'cookies' => [
        'access' => EnvHelper::env('AUTH_ACCESS_COOKIE', 'access_token'),
        'refresh' => EnvHelper::env('AUTH_REFRESH_COOKIE', 'refresh_token'),
        'secure' => filter_var(EnvHelper::env('AUTH_COOKIE_SECURE', true), FILTER_VALIDATE_BOOL),
        'same_site' => EnvHelper::env('AUTH_COOKIE_SAME_SITE', 'Strict'),
    ],
    'tables' => [
        'users' => EnvHelper::env('AUTH_USERS_TABLE', 'users'),
        'refresh_tokens' => EnvHelper::env('AUTH_REFRESH_TOKENS_TABLE', 'refresh_tokens'),
    ],
];
