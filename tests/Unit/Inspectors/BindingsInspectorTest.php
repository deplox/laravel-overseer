<?php

declare(strict_types=1);

use Deplox\Overseer\Inspectors\BindingsInspector;

test('returns an array', function (): void {
    $result = (new BindingsInspector)->inspect($this->app);

    expect($result)->toBeArray();
});

test('each entry has resolved and singleton keys', function (): void {
    $this->app->singleton('__test.binding', fn () => new stdClass);

    $result = (new BindingsInspector)->inspect($this->app);

    foreach ($result as $entry) {
        expect($entry)
            ->toHaveKey('resolved')
            ->toHaveKey('singleton');
        expect($entry['resolved'])->toBeBool();
        expect($entry['singleton'])->toBeBool();
    }
});

test('singleton binding reports singleton as true', function (): void {
    $this->app->singleton('__test.singleton', fn () => new stdClass);

    $result = (new BindingsInspector)->inspect($this->app);

    expect($result['__test.singleton']['singleton'])->toBeTrue();
});

test('non-singleton binding reports singleton as false', function (): void {
    $this->app->bind('__test.transient', fn () => new stdClass);

    $result = (new BindingsInspector)->inspect($this->app);

    expect($result['__test.transient']['singleton'])->toBeFalse();
});

test('resolved is true after the binding is resolved', function (): void {
    $this->app->bind('__test.resolved', fn () => new stdClass);
    $this->app->make('__test.resolved');

    $result = (new BindingsInspector)->inspect($this->app);

    expect($result['__test.resolved']['resolved'])->toBeTrue();
});

test('resolved is false before the binding is resolved', function (): void {
    $this->app->bind('__test.unresolved', fn () => new stdClass);

    $result = (new BindingsInspector)->inspect($this->app);

    expect($result['__test.unresolved']['resolved'])->toBeFalse();
});
