<?php

declare(strict_types=1);

namespace Deplox\Overseer\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array{php: string, laravel: string, composer: string, database: string} environment()
 * @method static array<string, array{loaded: bool, deferred: bool, provides: string[]}> providers()
 * @method static array<string, string[]> aliases()
 * @method static array<string, array{resolved: bool, singleton: bool}> bindings()
 * @method static array<string, string> instances()
 * @method static array<string, int> extenders()
 * @method static array{routes: array<string, array<string, mixed>>, middlewares: array<string, mixed>} router()
 * @method static Collection<array-key, mixed> inspect()
 * @method static array<array-key, mixed> toArray()
 *
 * @see \Deplox\Overseer\OverseerManager
 */
final class Overseer extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'overseer';
    }
}
