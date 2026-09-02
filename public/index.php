<?php

declare(strict_types=1);

// configure to continue processing even if the client disconnects
ignore_user_abort(true);

require_once __DIR__ . '/../vendor/autoload.php';

use Atria\System\App;

$app = new App(__DIR__ . '/../config');
$app->run();
