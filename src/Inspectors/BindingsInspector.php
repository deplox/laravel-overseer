<?php

declare(strict_types=1);

namespace Deplox\Overseer\Inspectors;

use Deplox\Overseer\Contracts\Inspector;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;

final class BindingsInspector implements Inspector
{
    /**
     * @return array<string, array{resolved: bool, singleton: bool}>
     */
    public function inspect(Application $app): array
    {
        return Arr::map($app->getBindings(), fn ($concrete, $abstract): array => [
            'resolved' => $app->resolved($abstract),
            'singleton' => $concrete['shared'],
        ]);
    }
}
