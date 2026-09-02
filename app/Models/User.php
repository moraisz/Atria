<?php

declare(strict_types=1);

namespace App\Models;

use Atria\Database\AbstractClasses\Model;
use Atria\Http\Exceptions\HttpException;

final class User extends Model
{
    protected static function table(): string
    {
        return 'users';
    }

    protected static function fillable(): array
    {
        return ['name', 'email', 'password_hash'];
    }

    /** @return array<string, mixed>|null */
    public static function findByEmail(string $email): ?array
    {
        return static::queryBuilder()
            ->select(['*'])
            ->from(static::table())
            ->where('email', '=', $email)
            ->first();
    }

    /** @return array<string, mixed> */
    public static function register(string $name, string $email, string $password): array
    {
        if (static::findByEmail($email) !== null) {
            throw new HttpException('Email already registered.', 422);
        }

        return static::create([
            'name' => $name,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);
    }
}
