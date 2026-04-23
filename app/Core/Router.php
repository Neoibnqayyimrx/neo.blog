<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Router
 *
 * Tiny front-controller router. Routes are registered with method + path,
 * and dispatched to "Controller@action" strings. Supports `{id}` parameters
 * and a group-prefix helper used by the admin routes.
 */
final class Router
{
    /** @var array<int, array{method:string, path:string, handler:string}> */
    private array $routes = [];
    private string $prefix = '';

    public function get(string $path, string $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, string $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    /** Register a matching GET + POST at the same path. */
    public function any(string $path, string $handler): void
    {
        $this->add('GET',  $path, $handler);
        $this->add('POST', $path, $handler);
    }

    /**
     * Register all routes inside $callback under a shared URL prefix.
     */
    public function group(string $prefix, callable $callback): void
    {
        $previous    = $this->prefix;
        $this->prefix = rtrim($previous . $prefix, '/');
        $callback($this);
        $this->prefix = $previous;
    }

    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);
        $path   = $this->normalise($uri);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;
            if ($route['path'] === $path) {
                $this->invoke($route['handler'], []);
                return;
            }

            // Parameterised routes — {id}, {slug}, etc.
            if (str_contains($route['path'], '{')) {
                $pattern = '#^' . preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[^/]+)', $route['path']) . '$#';
                if (preg_match($pattern, $path, $matches)) {
                    $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                    $this->invoke($route['handler'], $params);
                    return;
                }
            }
        }

        $this->notFound();
    }

    // ── Internals ────────────────────────────────────────────────────────────

    private function add(string $method, string $path, string $handler): void
    {
        $fullPath = rtrim($this->prefix . '/' . ltrim($path, '/'), '/');
        if ($fullPath === '') $fullPath = '/';
        $this->routes[] = [
            'method'  => $method,
            'path'    => $fullPath,
            'handler' => $handler,
        ];
    }

    private function normalise(string $uri): string
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        // Strip the application base path so routes are defined against "/"
        if (defined('ROOT_URL') && ROOT_URL !== '/' && str_starts_with($path, ROOT_URL)) {
            $path = '/' . ltrim(substr($path, strlen(ROOT_URL)), '/');
        }
        $path = rtrim($path, '/');
        return $path === '' ? '/' : $path;
    }

    private function invoke(string $handler, array $params): void
    {
        [$controller, $action] = explode('@', $handler, 2);
        $class = str_starts_with($controller, 'App\\') ? $controller : 'App\\Controllers\\' . $controller;
        if (!class_exists($class)) {
            throw new \RuntimeException("Controller not found: $class");
        }
        $instance = new $class();
        if (!method_exists($instance, $action)) {
            throw new \RuntimeException("Method $action not found on $class");
        }
        $instance->$action($params);
    }

    private function notFound(): void
    {
        http_response_code(404);
        View::render('errors/404', ['title' => 'Page not found']);
    }
}
