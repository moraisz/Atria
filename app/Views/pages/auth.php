<?php

declare(strict_types=1);

namespace App\Views\pages;

use Atria\Modules\View\ViewManager;

/**
 * @var ViewManager $this
 * @var string $type
 * @var string|null $error
 */

$isLogin = $type === 'login';

?>

<?php $this->extends('layouts/app') ?>

<?php $this->section('content') ?>

<div class="mx-auto mt-10 max-w-md">
    <div class="rounded-lg bg-white p-8 shadow-md">
        <h1 class="mb-6 text-2xl font-bold text-gray-900">
            <?= $isLogin ? 'Entrar' : 'Criar Conta' ?>
        </h1>

        <form method="POST" action="<?= $isLogin ? '/login' : '/register' ?>" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= $this->csrfToken() ?>">
            <?php if (!$isLogin) : ?>
                <?php $this->include('components/input', [
                    'name' => 'name',
                    'label' => 'Nome',
                    'placeholder' => 'Seu nome',
                    'required' => true,
                ]) ?>
            <?php endif; ?>

            <?php $this->include('components/input', [
                'name' => 'email',
                'label' => 'Email',
                'type' => 'email',
                'placeholder' => 'seu@email.com',
                'required' => true,
            ]) ?>

            <?php $this->include('components/input', [
                'name' => 'password',
                'label' => 'Senha',
                'type' => 'password',
                'placeholder' => '••••••••',
                'required' => true,
            ]) ?>

            <?php if ($error !== null) : ?>
                <p class="text-sm text-red-600"><?= $this->e($error) ?></p>
            <?php endif; ?>

            <button
                type="submit"
                class="w-full rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
                <?= $isLogin ? 'Entrar' : 'Criar Conta' ?>
            </button>
        </form>

        <p class="mt-4 text-center text-sm text-gray-600">
            <?php if ($isLogin) : ?>
                Não tem conta?
                <a href="/register" class="font-medium text-blue-600 hover:text-blue-500">Cadastre-se</a>
            <?php else : ?>
                Já tem conta?
                <a href="/login" class="font-medium text-blue-600 hover:text-blue-500">Entrar</a>
            <?php endif; ?>
        </p>
    </div>
</div>

<?php $this->endSection() ?>
