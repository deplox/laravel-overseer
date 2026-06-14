<?php

declare(strict_types=1);

use Deplox\Overseer\Facades\Overseer;
use Deplox\Overseer\OverseerManager;
use Illuminate\Support\Collection;

test('Overseer facade resolves to OverseerManager', function (): void {
    expect(Overseer::getFacadeRoot())->toBeInstanceOf(OverseerManager::class);
});

test('Overseer::environment() returns expected keys', function (): void {
    expect(Overseer::environment())->toHaveKeys(['php', 'laravel', 'composer', 'database']);
});

test('Overseer::providers() returns a non-empty array', function (): void {
    expect(Overseer::providers())->toBeArray()->not->toBeEmpty();
});

test('Overseer::aliases() returns an array', function (): void {
    expect(Overseer::aliases())->toBeArray();
});

test('Overseer::bindings() returns a non-empty array', function (): void {
    expect(Overseer::bindings())->toBeArray()->not->toBeEmpty();
});

test('Overseer::instances() returns a non-empty array', function (): void {
    expect(Overseer::instances())->toBeArray()->not->toBeEmpty();
});

test('Overseer::extenders() returns an array', function (): void {
    expect(Overseer::extenders())->toBeArray();
});

test('Overseer::router() returns routes and middlewares', function (): void {
    expect(Overseer::router())->toHaveKeys(['routes', 'middlewares']);
});

test('Overseer::inspect() returns a Collection', function (): void {
    expect(Overseer::inspect())->toBeInstanceOf(Collection::class);
});

test('Overseer::toArray() returns a plain array', function (): void {
    expect(Overseer::toArray())->toBeArray();
});
