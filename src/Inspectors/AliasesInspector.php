<?php

declare(strict_types=1);

namespace Deplox\Overseer\Inspectors;

use Deplox\Overseer\Contracts\Inspector;
use Illuminate\Contracts\Foundation\Application;
use ReflectionClass;
use ReflectionException;

final class AliasesInspector implements Inspector
{
    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<string, string[]>
     */
    public function inspect(Application $app): array
    {
        try {
            $property = new ReflectionClass($app)->getProperty('abstractAliases');
        } catch (ReflectionException) {
            return [];
        }

        return $property->getValue($app);
    }
}
