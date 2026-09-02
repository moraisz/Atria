<?php

declare(strict_types=1);

use Atria\Helpers\EnvHelper;

return [
    'enabled' => filter_var(EnvHelper::env('MERCURE_ENABLED', true), FILTER_VALIDATE_BOOL),
    'hub_url' => EnvHelper::env('MERCURE_HUB_URL', EnvHelper::env('MERCURE_PUBLIC_URL', '/.well-known/mercure')),
    'subscribe_jwt_key' => EnvHelper::env('MERCURE_SUBSCRIBER_JWT_KEY', EnvHelper::env('MERCURE_SUBSCRIBE_JWT_KEY', '')),
    'subscribe_jwt_ttl' => max(1, (int) EnvHelper::env('MERCURE_SUBSCRIBE_JWT_TTL', 3600)),
    'authorization_cookie_domain' => EnvHelper::env('MERCURE_AUTHORIZATION_COOKIE_DOMAIN', ''),
];
