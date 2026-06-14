<?php

declare(strict_types=1);

use Deplox\Overseer\Inspectors\RouterInspector;

test('returns array with routes and middlewares keys', function (): void {
    $result = (new RouterInspector)->inspect($this->app);

    expect($result)->toHaveKeys(['routes', 'middlewares']);
});

test('middlewares has groups, aliases, and priority keys', function (): void {
    $result = (new RouterInspector)->inspect($this->app);

    expect($result['middlewares'])->toHaveKeys(['groups', 'aliases', 'priority']);
});

test('registered route appears under its URI key', function (): void {
    $this->app['router']->get('/__unit-probe', fn () => 'ok');

    $routes = (new RouterInspector)->inspect($this->app)['routes'];

    // Laravel adds HEAD to every GET route, so the method key is "GET|HEAD".
    expect($routes)->toHaveKey('__unit-probe');
    expect($routes['__unit-probe'])->toHaveKey('GET|HEAD');
});

test('route entry contains the expected fields', function (): void {
    $this->app['router']->get('/__unit-fields', fn () => 'ok')->name('unit.fields');

    $routes = (new RouterInspector)->inspect($this->app)['routes'];
    $entry = $routes['__unit-fields']['GET|HEAD'];

    expect($entry)->toHaveKeys([
        'name', 'method', 'uri', 'action',
        'fallback', 'defaults', 'wheres',
        'bindingFields', 'lockSeconds', 'waitSeconds', 'withTrashed',
    ]);
    expect($entry['name'])->toBe('unit.fields');
    expect($entry['uri'])->toBe('__unit-fields');
});

test('uri containing dots is stored under the full literal key', function (): void {
    $this->app['router']->get('/api/v1.0/status', fn () => 'ok');

    $routes = (new RouterInspector)->inspect($this->app)['routes'];

    // Regression: Arr::set previously split "api/v1.0/status" on the dot,
    // nesting under "api/v1" → "0/status" instead of the literal URI.
    expect($routes)->toHaveKey('api/v1.0/status');
    expect($routes['api/v1.0/status'])->toHaveKey('GET|HEAD');
});

test('closure actions are serialized to the string "closure"', function (): void {
    $this->app['router']->get('/__unit-closure', fn () => 'ok');

    $routes = (new RouterInspector)->inspect($this->app)['routes'];

    expect($routes['__unit-closure']['GET|HEAD']['action']['uses'])->toBe('closure');
});

test('output is fully JSON-encodable', function (): void {
    $this->app['router']->get('/__unit-json', fn () => 'ok');

    $result = (new RouterInspector)->inspect($this->app);

    $json = json_encode($result, JSON_THROW_ON_ERROR);
    expect($json)->toBeString();
});
