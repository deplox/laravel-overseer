<?php

declare(strict_types=1);

use Deplox\Overseer\Inspectors\InstancesInspector;

test('returns an array', function (): void {
    $result = (new InstancesInspector)->inspect($this->app);

    expect($result)->toBeArray();
});

test('all values are strings', function (): void {
    $result = (new InstancesInspector)->inspect($this->app);

    foreach ($result as $value) {
        expect($value)->toBeString();
    }
});

test('returns the class name for a bound object instance', function (): void {
    $this->app->instance('__test.instance', new stdClass);

    $result = (new InstancesInspector)->inspect($this->app);

    expect($result['__test.instance'])->toBe('stdClass');
});

test('returns the string value for a bound string instance', function (): void {
    $this->app->instance('__test.string', 'some-value');

    $result = (new InstancesInspector)->inspect($this->app);

    expect($result['__test.string'])->toBe('some-value');
});
