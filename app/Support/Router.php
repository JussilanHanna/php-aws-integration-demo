<?php
declare(strict_types=1);

namespace App\Support;

use Closure;
use ReflectionFunction;

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

    public function dispatch(string $method, string $path)
    {
        $routes = $this->routes[$method] ?? [];

        foreach ($routes as $routePath => $handler) {
            // "/bookings/{id}/files" -> ^/bookings/(?P<id>[^/]+)/files$
            $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $routePath);
            $pattern = '#^' . $pattern . '$#';

            if (!preg_match($pattern, $path, $matches)) {
                continue;
            }

            $params = [];
            foreach ($matches as $k => $v) {
                if (!is_int($k)) $params[$k] = $v;
            }

            try {
                // Kutsutaan handleria oikein riippuen siitä, odottaako se parametreja vai ei
                $fn = new ReflectionFunction(Closure::fromCallable($handler));
                if ($fn->getNumberOfParameters() >= 1) {
                    return $handler($params);
                }
                return $handler();
            } catch (\Throwable $e) {
                error_log((string)$e);

                $env = \App\Support\Env::get('APP_ENV', 'prod');
                if ($env === 'local') {
                    \App\Support\Http::error('Internal Server Error', 500, [
                        'exception' => get_class($e),
                        'message' => $e->getMessage(),
                    ]);
                }

                \App\Support\Http::error('Internal Server Error', 500);
                return null;
            }
        }

        http_response_code(404);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(
            ['error' => 'Not Found', 'path' => $path, 'method' => $method],
            JSON_UNESCAPED_UNICODE
        );
        return null;
    }
}
