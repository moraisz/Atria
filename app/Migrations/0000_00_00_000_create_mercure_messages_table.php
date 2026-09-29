<?php

declare(strict_types=1);

namespace App\Migrations;

use Atria\Database\AbstractClasses\Migration;
use Atria\Database\Schema\Blueprint;

return new class extends Migration {
    public function up(): void
    {
        $this->schema->create('mercure_messages', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id');
            $table->string('topic');
            $table->text('payload');
            $table->string('event_type', 100);
            $table->boolean('is_private')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('published_at')->nullable();
            $table->index(['user_id'], 'idx_mercure_messages_user_id');
            $table->index(['created_at'], 'idx_mercure_messages_created_at');
        });
    }

    public function down(): void
    {
        $this->schema->drop('mercure_messages');
    }
};
