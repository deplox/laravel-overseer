<?php

declare(strict_types=1);

use Deplox\Overseer\Inspectors\ExtendersInspector;

test('returns an array', function (): void {
    $result = (new ExtendersInspector)->inspect($this->app);

    expect($result)->toBeArray();
});

test('all values are integers', function (): void {
    $this->app->extend('stdClass', fn ($i) => $i);

    $result = (new ExtendersInspector)->inspect($this->app);

    foreach ($result as $count) {
        expect($count)->toBeInt();
    }
});

test('records a single extender for an abstract', function (): void {
    $this->app->extend('__test.extended', fn ($i) => $i);

    $result = (new ExtendersInspector)->inspect($this->app);

    expect($result['__test.extended'])->toBe(1);
});

test('counts multiple extenders registered for the same abstract', function (): void {
    $this->app->extend('__test.multi', fn ($i) => $i);
    $this->app->extend('__test.multi', fn ($i) => $i);

    $result = (new ExtendersInspector)->inspect($this->app);

    expect($result['__test.multi'])->toBe(2);
});
