<?php

declare(strict_types=1);

namespace App\Views\components;

use Atria\Modules\View\ViewManager;

/**
 * @var ViewManager $this
 * @var string $title
 * @var string|null $type
 */

?>

<?php $type ??= 'button' ?>

<button
    type="<?= $this->e($type) ?>"
    class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
>
    <?= $this->e($title) ?>
</button>
