<?php

declare(strict_types=1);

namespace Deplox\Overseer\Tests;

use Deplox\Overseer\OverseerServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [OverseerServiceProvider::class];
    }
}
