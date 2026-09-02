<?php

declare(strict_types=1);

namespace App\Views\components;

use Atria\Modules\View\ViewManager;

/**
 * @var ViewManager $this
 * @var string $name
 * @var string $label
 * @var string|null $type
 * @var string|null $placeholder
 * @var bool|null $required
 */

$type ??= 'text';
$placeholder ??= '';
$required ??= false;

?>

<label class="block">
    <span class="text-gray-700 text-sm font-medium"><?= $this->e($label) ?></span>
    <input
        type="<?= $this->e($type) ?>"
        name="<?= $this->e($name) ?>"
        placeholder="<?= $this->e($placeholder) ?>"
        <?= $required ? 'required' : '' ?>
        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
    >
</label>
