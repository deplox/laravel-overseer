<?php

declare(strict_types=1);

use Deplox\Overseer\Inspectors\ProvidersInspector;
use Deplox\Overseer\OverseerServiceProvider;

test('returns a non-empty array', function (): void {
    $result = (new ProvidersInspector)->inspect($this->app);

    expect($result)->toBeArray()->not->toBeEmpty();
});

test('each entry has the expected shape', function (): void {
    $result = (new ProvidersInspector)->inspect($this->app);

    foreach ($result as $entry) {
        expect($entry)
            ->toHaveKey('loaded')
            ->toHaveKey('deferred')
            ->toHaveKey('provides');
        expect($entry['loaded'])->toBeBool();
        expect($entry['deferred'])->toBeBool();
        expect($entry['provides'])->toBeArray();
    }
});

test('OverseerServiceProvider is present and marked as loaded', function (): void {
    $result = (new ProvidersInspector)->inspect($this->app);

    expect($result)->toHaveKey(OverseerServiceProvider::class);
    expect($result[OverseerServiceProvider::class]['loaded'])->toBeTrue();
});

test('OverseerServiceProvider is not deferred', function (): void {
    $result = (new ProvidersInspector)->inspect($this->app);

    expect($result[OverseerServiceProvider::class]['deferred'])->toBeFalse();
});
