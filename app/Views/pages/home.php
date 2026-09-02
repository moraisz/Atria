<?php

declare(strict_types=1);

namespace App\Views\pages;

use Atria\Modules\View\ViewManager;

/**
 * @var ViewManager $this
 * @var array<string, mixed>|null $user
 * @var string $mercureTopic
 * @var string $mercureSubscribeUrl
 * @var string $mercureDataExample
 * @var array<int, array{id: int, user_id: int, message: string, created_at: string, type: string}> $mercureHistory
 */

?>

<?php
$mercureDemoConfig = json_encode([
    'subscribeUrl' => $mercureSubscribeUrl,
    'type' => 'user.message',
    'history' => $mercureHistory,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>

<?php $this->extends('layouts/app') ?>

<?php $this->section('content') ?>

    <div
        x-data="mercureDemo(<?= htmlspecialchars($mercureDemoConfig !== false ? $mercureDemoConfig : '{}', ENT_QUOTES, 'UTF-8') ?>)"
        x-init="connect()"
        class="mx-auto max-w-6xl px-4 py-6"
    >
        <header class="flex flex-col gap-4 border-b border-gray-200 pb-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-blue-600">Atria + Mercure</p>
                <h1 class="text-2xl font-semibold text-gray-900">
                    Olá, <?= $this->e(is_array($user) ? ($user['name'] ?? 'Usuário') : 'Usuário') ?>
                </h1>
                <p class="mt-1 text-sm text-gray-600">Use esta tela para testar publish e subscribe em tópicos privados em tempo real.</p>
            </div>

            <form method="POST" action="/logout">
                <input type="hidden" name="csrf_token" value="<?= $this->csrfToken() ?>">
                <?php $this->include('components/button', [
                    'title' => 'Sair',
                    'type' => 'submit',
                ]) ?>
            </form>
        </header>

        <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1.1fr)_minmax(0,0.9fr)]">
            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Subscribe</h2>
                        <p class="mt-1 text-sm text-gray-600">Conecte no hub Mercure e acompanhe as mensagens recebidas nesse navegador.</p>
                    </div>
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                        :class="connected ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'"
                        x-text="connected ? 'Conectado' : 'Desconectado'"
                    ></span>
                </div>

                <div class="mt-5 space-y-4">
                    <label class="block">
                        <span class="text-sm font-medium text-gray-700">Tópico</span>
                        <input value="<?= $this->e($mercureTopic) ?>" type="text" readonly class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm shadow-sm">
                    </label>

                    <div class="rounded-xl bg-gray-50 p-4 text-sm text-gray-700">
                        <p class="font-medium text-gray-900">URL de subscribe</p>
                        <p class="mt-2 break-all font-mono text-xs text-gray-600"><?= $this->e($mercureSubscribeUrl) ?></p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button type="button" @click="connect()" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Conectar</button>
                        <button type="button" @click="disconnect()" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Desconectar</button>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Publish</h2>
                <p class="mt-1 text-sm text-gray-600">Envie um update para o hub usando o publisher nativo do framework.</p>

                <form method="POST" action="/mercure/publish" @submit.prevent="publish($event)" class="mt-5 space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= $this->csrfToken() ?>">
                    <label class="block">
                        <span class="text-sm font-medium text-gray-700">Message</span>
                        <textarea name="data" rows="8" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 font-mono text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"><?= $this->e($mercureDataExample) ?></textarea>
                    </label>

                    <button type="submit" :disabled="publishing" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-60">
                        <span x-text="publishing ? 'Publicando...' : 'Publicar mensagem'"></span>
                    </button>
                </form>
            </section>
        </div>

        <section class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Messages</h2>
                    <p class="mt-1 text-sm text-gray-600">As mensagens aparecem aqui assim que o Mercure entregar o evento.</p>
                </div>
                <button type="button" @click="messages = []" class="rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Limpar</button>
            </div>

            <template x-if="error">
                <p class="mt-4 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700" x-text="error"></p>
            </template>

            <div class="mt-4 space-y-3">
                <template x-if="messages.length === 0">
                    <div class="rounded-xl border border-dashed border-gray-300 px-4 py-8 text-center text-sm text-gray-500">
                        Nenhuma mensagem ainda. Publique um evento para testar o fluxo.
                    </div>
                </template>

                <template x-for="message in messages" :key="message.id">
                    <article class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-gray-500">
                            <span class="font-semibold uppercase tracking-wide text-gray-700" x-text="message.type || 'message'"></span>
                            <span x-text="message.created_at"></span>
                        </div>
                        <pre class="mt-3 overflow-x-auto whitespace-pre-wrap break-words text-sm text-gray-800" x-text="message.message"></pre>
                    </article>
                </template>
            </div>
        </section>
    </div>

<?php $this->endSection() ?>
