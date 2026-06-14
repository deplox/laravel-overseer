<?php

declare(strict_types=1);

use Deplox\Overseer\OverseerManager;
use Illuminate\Support\Collection;

beforeEach(function (): void {
    $this->overseer = new OverseerManager($this->app);
});

test('inspect returns a collection with the expected top-level keys', function (): void {
    $result = $this->overseer->inspect();

    expect($result)->toBeInstanceOf(Collection::class)
        ->and($result->keys()->all())->toBe([
            'environment',
            'providers',
            'aliases',
            'bindings',
            'instances',
            'extenders',
            'router',
        ]);
});

test('environment returns expected metadata keys and types', function (): void {
    $env = $this->overseer->environment();

    expect($env)
        ->toBeArray()
        ->toHaveKeys(['php', 'laravel', 'composer', 'database'])
        ->and($env['php'])->toBe(PHP_VERSION)
        ->and($env['laravel'])->toBeString()->not->toBeEmpty()
        ->and($env['composer'])->toBeString()
        ->and($env['database'])->toBeString();
});

test('providers returns a non-empty array of registered service providers', function (): void {
    $providers = $this->overseer->providers();

    expect($providers)->toBeArray()->not->toBeEmpty();
});

test('bindings returns the application container bindings', function (): void {
    $bindings = $this->overseer->bindings();

    expect($bindings)->toBeArray()->not->toBeEmpty();
});

test('aliases returns container aliases', function (): void {
    $aliases = $this->overseer->aliases();

    expect($aliases)->toBeArray();
});

test('instances returns container resolved instances', function (): void {
    $instances = $this->overseer->instances();

    expect($instances)->toBeArray()->not->toBeEmpty();
});

test('extenders returns container extenders', function (): void {
    $extenders = $this->overseer->extenders();

    expect($extenders)->toBeArray();
});

test('router returns routes and middlewares structure', function (): void {
    $this->app['router']->get('/__overseer-probe', fn () => 'ok');

    $result = $this->overseer->router();

    expect($result)
        ->toBeArray()
        ->toHaveKeys(['routes', 'middlewares'])
        ->and($result['routes'])->not->toBeEmpty();
});

test('toArray serializes inspect() output to nested plain arrays', function (): void {
    $array = $this->overseer->toArray();

    expect($array)
        ->toBeArray()
        ->toHaveKey('environment')
        ->toHaveKey('router');

    // Verify no nested Collection objects survive serialization.
    array_walk_recursive($array, function (mixed $value): void {
        expect($value)->not->toBeInstanceOf(Collection::class);
    });
});

test('router inspector replaces closure actions with the string "closure"', function (): void {
    $this->app['router']->get('/__overseer-closure-route', fn () => 'ok');

    $routes = $this->overseer->router()['routes'] ?? [];

    $found = false;

    array_walk_recursive($routes, function (mixed $value, mixed $key) use (&$found): void {
        if ($key === 'uses' && $value === 'closure') {
            $found = true;
        }
    });

    expect($found)->toBeTrue();
});

test('router inspector output is JSON-encodable', function (): void {
    $this->app['router']->get('/__overseer-json-route', fn () => 'ok');

    $json = json_encode($this->overseer->router(), JSON_THROW_ON_ERROR);

    expect($json)->toBeString();
});

test('uri with dots is not incorrectly nested in router output', function (): void {
    $this->app['router']->get('/api/v2.1/health', fn () => 'ok');

    $routes = $this->overseer->router()['routes'];

    // Regression: Arr::set previously split "api/v2.1/health" on the dot.
    // Laravel also appends HEAD to GET routes, so the method key is "GET|HEAD".
    expect($routes)->toHaveKey('api/v2.1/health');
    expect($routes['api/v2.1/health'])->toHaveKey('GET|HEAD');
});

test('route entry includes per-route middleware', function (): void {
    $this->app['router']->get('/__overseer-mw', fn () => 'ok')->middleware('auth');

    $entry = $this->overseer->router()['routes']['__overseer-mw']['GET|HEAD'];

    expect($entry['middleware'])->toBeArray()->toContain('auth');
});
