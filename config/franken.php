<?php

declare(strict_types=1);

use Atria\Helpers\EnvHelper;

return [
    'worker_mode' => EnvHelper::env('WORKER_MODE', true),
    'early_hints' => EnvHelper::env('EARLY_HINTS', false),
    'max_requests' => EnvHelper::env('MAX_REQUESTS', 1000),
];
