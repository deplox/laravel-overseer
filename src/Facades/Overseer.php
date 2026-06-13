<?php

declare(strict_types=1);

namespace Deplox\Overseer\Facades;

use Illuminate\Support\Facades\Facade;

final class Overseer extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'overseer';
    }
}
