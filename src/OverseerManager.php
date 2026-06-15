<?php

declare(strict_types=1);

namespace Deplox\Overseer;

use Deplox\Overseer\Inspectors\AliasesInspector;
use Deplox\Overseer\Inspectors\BindingsInspector;
use Deplox\Overseer\Inspectors\EnvironmentInspector;
use Deplox\Overseer\Inspectors\ExtendersInspector;
use Deplox\Overseer\Inspectors\InstancesInspector;
use Deplox\Overseer\Inspectors\ProvidersInspector;
use Deplox\Overseer\Inspectors\RouterInspector;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

/**
 * @implements Arrayable<array-key, mixed>
 **/
final readonly class OverseerManager implements Arrayable
{
    public function __construct(
        private Application $app,
    ) {}

    /** @return array{php: string, laravel: string, composer: string, database: string} */
    public function environment(): array
    {
        return (new EnvironmentInspector)->inspect($this->app);
    }

    /** @return array<string, array{loaded: bool, deferred: bool, provides: string[]}> */
    public function providers(): array
    {
        return (new ProvidersInspector)->inspect($this->app);
    }

    /** @return array<string, string[]> */
    public function aliases(): array
    {
        return (new AliasesInspector)->inspect($this->app);
    }

    /** @return array<string, array{resolved: bool, singleton: bool}> */
    public function bindings(): array
    {
        return (new BindingsInspector)->inspect($this->app);
    }

    /** @return array<string, string> */
    public function instances(): array
    {
        return (new InstancesInspector)->inspect($this->app);
    }

    /** @return array<string, int> */
    public function extenders(): array
    {
        return (new ExtendersInspector)->inspect($this->app);
    }

    /** @return array{routes: array<string, array<string, mixed>>, middlewares: array{groups: array<string, array<string>>, aliases: array<string, string>, priority: array<int, string>}} */
    public function router(): array
    {
        return (new RouterInspector)->inspect($this->app);
    }

    /** @return Collection<string, mixed> */
    public function inspect(): Collection
    {
        return new Collection([
            'environment' => $this->environment(),
            'providers' => $this->providers(),
            'aliases' => collect($this->aliases())->sortKeys()->all(),
            'bindings' => collect($this->bindings())->sortKeys()->all(),
            'instances' => collect($this->instances())->sortKeys()->all(),
            'extenders' => collect($this->extenders())->sortKeys()->all(),
            'router' => $this->router(),
        ]);
    }

    public function toArray(): array
    {
        return $this->inspect()->toArray();
    }
}
