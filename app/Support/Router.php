<?php
declare(strict_types=1);

namespace App\Support;

final class Router
{
    /** @var array<string, array<string, callable>> */
    private array $routes = [];

    public function get(string $path, callable $handler): void { $this->map('GET', $path, $handler); }
    public function post(string $path, callable $handler): void { $this->map('POST', $path, $handler); }

    private function map(string $method, string $path, callable $handler): void
    {
        $this->routes[$method][$path] = $handler;
    }

    public function dispatch(string $method, string $uriPath): void
    {
        $path = rtrim($uriPath, '/') ?: '/';
        $method = strtoupper($method);

        $handler = $this->routes[$method][$path] ?? null;
        if (!$handler) {
            Http::error('Not Found', 404, ['path' => $path, 'method' => $method]);
        }

        try {
            $handler();
        } catch (\Throwable $e) {
            error_log($e); // CloudWatch picks this up when running under web server logs
            Http::error('Internal Server Error', 500);
        }
    }
}
