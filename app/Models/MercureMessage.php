<?php

declare(strict_types=1);

namespace App\Models;

use Atria\Database\AbstractClasses\Model;

final class MercureMessage extends Model
{
    protected static function table(): string
    {
        return 'mercure_messages';
    }

    protected static function fillable(): array
    {
        return ['user_id', 'topic', 'payload', 'event_type', 'is_private'];
    }

    /** @return array<string, mixed> */
    public static function record(int $userId, string $topic, string $payload, string $eventType): array
    {
        return static::create([
            'user_id' => $userId,
            'topic' => $topic,
            'payload' => $payload,
            'event_type' => $eventType,
            'is_private' => true,
        ]);
    }

    public static function markPublished(int $id): void
    {
        static::queryBuilder()
            ->update(static::table())
            ->set(['published_at' => date('Y-m-d H:i:s')])
            ->where('id', '=', $id)
            ->execute();
    }

    /** @return array<int, array<string, mixed>> */
    public static function forUser(int $userId): array
    {
        return static::queryBuilder()
            ->select(['*'])
            ->from(static::table())
            ->where('user_id', '=', $userId)
            ->orderBy('created_at', 'DESC')
            ->execute();
    }
}
