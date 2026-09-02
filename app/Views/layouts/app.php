<?php

declare(strict_types=1);

namespace App\Views\layouts;

use Atria\Modules\View\ViewManager;

/**
 * @var ViewManager $this
 * @var string $title
 */

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="/assets/favicon.svg">
    <title><?= htmlspecialchars($title) ?></title>
    <?= $this->yield('head') ?>
    <?= $this->viteTags() ?>
</head>
<body>
    <div id="app">
        <?= $this->yield('content') ?>
    </div>
</body>
</html>
