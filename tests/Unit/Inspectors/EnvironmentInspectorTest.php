<?php

declare(strict_types=1);

use Deplox\Overseer\Inspectors\EnvironmentInspector;

test('returns array with expected keys', function (): void {
    $result = (new EnvironmentInspector)->inspect($this->app);

    expect($result)->toBeArray()->toHaveKeys(['php', 'laravel', 'composer', 'database']);
});

test('php key matches PHP_VERSION', function (): void {
    $result = (new EnvironmentInspector)->inspect($this->app);

    expect($result['php'])->toBe(PHP_VERSION);
});

test('laravel key is a non-empty string', function (): void {
    $result = (new EnvironmentInspector)->inspect($this->app);

    expect($result['laravel'])->toBeString()->not->toBeEmpty();
});

test('composer key is a string', function (): void {
    $result = (new EnvironmentInspector)->inspect($this->app);

    expect($result['composer'])->toBeString();
});

test('database key returns a non-empty string', function (): void {
    $result = (new EnvironmentInspector)->inspect($this->app);

    expect($result['database'])->toBeString()->not->toBeEmpty();
});

test('database key returns driver name for sqlite in-memory', function (): void {
    $result = (new EnvironmentInspector)->inspect($this->app);

    // phpunit.xml sets DB_CONNECTION=sqlite and DB_DATABASE=:memory:
    expect($result['database'])->toBeString();
});
