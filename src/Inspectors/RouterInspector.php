<?php

declare(strict_types=1);

namespace Deplox\Overseer\Inspectors;

use Closure;
use Deplox\Overseer\Contracts\Inspector;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;

final class RouterInspector implements Inspector
{
    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return array{routes: array<string, array<string, mixed>>, middlewares: array{groups: array<string, array<string>>, aliases: array<string, string>, priority: array<int, string>}}
     */
    public function inspect(Application $app): array
    {
        /** @var \Illuminate\Routing\Router */
        $router = $app->make(\Illuminate\Routing\Router::class);

        /** @var \Illuminate\Routing\RouteCollection */
        $routes = $router->getRoutes();

        $data = [
            'routes' => [],
            'middlewares' => [
                'groups' => $router->getMiddlewareGroups(),
                'aliases' => $router->getMiddleware(),
                'priority' => $router->middlewarePriority,
            ],
        ];

        foreach ($routes->getRoutes() as $route) {
            $method = $route->methods() === $router::$verbs ? 'ANY' : implode('|', $route->methods());
            $uri = implode('/', array_filter([$route->getDomain(), $route->uri()]));

            // Direct assignment avoids Arr::set's dot-notation expansion, which
            // would incorrectly nest URIs containing dots (e.g. "api/v1.0/status").
            $data['routes'][$uri][$method] = [
                'name' => $route->getName(),
                'method' => $method,
                'uri' => $uri,
                'middleware' => $route->middleware(),
                'action' => $this->serializeAction($route->getAction()),
                'fallback' => $route->isFallback,
                'defaults' => $route->defaults,
                'wheres' => $route->wheres,
                'bindingFields' => $route->bindingFields(),
                'lockSeconds' => $route->locksFor(),
                'waitSeconds' => $route->waitsFor(),
                'withTrashed' => $route->allowsTrashedBindings(),
            ];
        }

        return $data;
    }

    /**
     * Serialize a route's action array, replacing closures and other
     * non-serializable callables with descriptive string placeholders so
     * the result can be JSON-encoded or cached.
     *
     * @param  array<string, mixed>  $action
     * @return array<string, mixed>
     */
    private function serializeAction(array $action): array
    {
        return Arr::map($action, fn (mixed $value): mixed => match (true) {
            $value instanceof Closure => 'closure',
            is_object($value) => $value::class,
            default => $value,
        });
    }
}
