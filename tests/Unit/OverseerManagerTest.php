<?php

declare(strict_types=1);

use Deplox\Overseer\OverseerManager;
use Illuminate\Support\Collection;

beforeEach(function (): void {
    $this->manager = new OverseerManager($this->app);
});

test('inspect returns a Collection', function (): void {
    expect($this->manager->inspect())->toBeInstanceOf(Collection::class);
});

test('all values inside inspect() are plain arrays, not nested Collections', function (): void {
    foreach ($this->manager->inspect() as $key => $value) {
        expect($value)->toBeArray("Value for '$key' should be a plain array");
    }
});

test('inspect() keys are sorted for aliases, bindings, instances, and extenders', function (): void {
    $result = $this->manager->inspect();

    foreach (['aliases', 'bindings', 'instances', 'extenders'] as $key) {
        $keys = array_keys($result->get($key));
        $sorted = $keys;
        sort($sorted);
        expect($keys)->toBe($sorted, "Expected '$key' to be sorted");
    }
});

test('toArray returns a plain PHP array', function (): void {
    expect($this->manager->toArray())->toBeArray();
});

test('toArray output is fully JSON-encodable', function (): void {
    $json = json_encode($this->manager->toArray(), JSON_THROW_ON_ERROR);
    expect($json)->toBeString();
});

test('facade resolves the same class as OverseerManager', function (): void {
    expect($this->app->make('overseer'))->toBeInstanceOf(OverseerManager::class);
});

test('facade binding is a singleton', function (): void {
    $a = $this->app->make('overseer');
    $b = $this->app->make('overseer');

    expect($a)->toBe($b);
});
