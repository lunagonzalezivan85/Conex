<?php

namespace App\Libraries;

/**
 * Holder del usuario autenticado por token API durante el request.
 * Lo llena ApiAuthFilter y lo leen los controllers de la API.
 */
class ApiAuth
{
    private static ?array $user = null;
    private static ?array $token = null;
    private static ?string $roleSlug = null;

    public static function login(array $user, array $token, ?string $roleSlug = null): void
    {
        self::$user = $user;
        self::$token = $token;
        self::$roleSlug = $roleSlug;
    }

    public static function user(): ?array
    {
        return self::$user;
    }

    public static function id(): ?int
    {
        return self::$user['id'] ?? null;
    }

    public static function token(): ?array
    {
        return self::$token;
    }

    public static function roleSlug(): ?string
    {
        return self::$roleSlug;
    }

    public static function check(): bool
    {
        return self::$user !== null;
    }
}
