<?php

declare(strict_types=1);

use Deplox\Overseer\Inspectors\AliasesInspector;

test('returns an array', function (): void {
    $result = (new AliasesInspector)->inspect($this->app);

    expect($result)->toBeArray();
});

test('each value is an array of alias strings', function (): void {
    $result = (new AliasesInspector)->inspect($this->app);

    foreach ($result as $aliases) {
        expect($aliases)->toBeArray();
        foreach ($aliases as $alias) {
            expect($alias)->toBeString();
        }
    }
});

test('keys are abstract class or interface names', function (): void {
    $result = (new AliasesInspector)->inspect($this->app);

    foreach (array_keys($result) as $abstract) {
        expect($abstract)->toBeString()->not->toBeEmpty();
    }
});

test('the "app" abstract has at least one class alias', function (): void {
    // abstractAliases maps short service names → array of class-name aliases.
    // 'app' is always present and points to at least Illuminate\Foundation\Application.
    $result = (new AliasesInspector)->inspect($this->app);

    expect($result)->toHaveKey('app');
    expect($result['app'])->toBeArray()->not->toBeEmpty();
});
