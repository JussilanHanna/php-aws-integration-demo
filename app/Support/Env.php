<?php
declare(strict_types=1);

namespace App\Support;

use Dotenv\Dotenv;

final class Env
{
    public static function load(string $rootPath): void
    {
        // Load .env if present (local/dev). On AWS you can use real env vars too.
        if (file_exists($rootPath . '/.env')) {
            $dotenv = Dotenv::createImmutable($rootPath);
            $dotenv->safeLoad();
        }
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $val = $_ENV[$key] ?? getenv($key) ?: null;
        return $val !== null && $val !== '' ? $val : $default;
    }
}
