<?php

declare(strict_types=1);

namespace Deplox\Overseer\Inspectors;

use Deplox\Overseer\Contracts\Inspector;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use ReflectionClass;
use ReflectionException;

final class ExtendersInspector implements Inspector
{
    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<string, int>
     */
    public function inspect(Application $app): array
    {
        try {
            $property = new ReflectionClass($app)->getProperty('extenders');
        } catch (ReflectionException) {
            return [];
        }

        return Arr::map($property->getValue($app), fn ($value): int => count($value));
    }
}
