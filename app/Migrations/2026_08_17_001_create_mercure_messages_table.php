<?php

declare(strict_types=1);

namespace App\Migrations;

use Atria\Database\AbstractClasses\Migration;

return new class extends Migration {
    public function up(): void
    {
        $this->queryBuilder
            ->createTable('mercure_messages', [
                'id' => 'SERIAL PRIMARY KEY',
                'user_id' => 'INTEGER NOT NULL',
                'topic' => 'VARCHAR(255) NOT NULL',
                'payload' => 'TEXT NOT NULL',
                'event_type' => 'VARCHAR(100) NOT NULL',
                'is_private' => 'BOOLEAN NOT NULL DEFAULT TRUE',
                'created_at' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
                'published_at' => 'TIMESTAMP NULL',
            ])
            ->execute();

        $this->queryBuilder
            ->createIndex('idx_mercure_messages_user_id', 'mercure_messages', ['user_id']);

        $this->queryBuilder
            ->createIndex('idx_mercure_messages_created_at', 'mercure_messages', ['created_at']);
    }

    public function down(): void
    {
        $this->queryBuilder
            ->dropTable('mercure_messages')
            ->execute();
    }
};
