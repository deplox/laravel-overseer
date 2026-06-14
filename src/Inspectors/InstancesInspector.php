<?php

declare(strict_types=1);

namespace Deplox\Overseer\Inspectors;

use Deplox\Overseer\Contracts\Inspector;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use ReflectionClass;
use ReflectionException;

final class InstancesInspector implements Inspector
{
    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<string, string>
     */
    public function inspect(Application $app): array
    {
        try {
            $property = new ReflectionClass($app)->getProperty('instances');
        } catch (ReflectionException) {
            return [];
        }

        return Arr::map(
            $property->getValue($app),
            fn (mixed $instance): string => match (true) {
                is_object($instance) => $instance::class,
                is_string($instance) => $instance,
                default => get_debug_type($instance),
            },
        );
    }
}
