<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Support\Env;
use App\Support\Router;

$root = dirname(__DIR__);
Env::load($root);

$router = new Router();

// Load routes
$register = require $root . '/routes/api.php';
$register($router);

// Dispatch
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($uri, PHP_URL_PATH) ?: '/';

$router->dispatch($method, $path);
