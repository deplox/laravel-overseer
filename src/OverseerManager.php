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
    /**
     * @param  \Illuminate\Foundation\Application  $app
     */
    public function __construct(
        private Application $app,
    ) {}

    public function environment(): array
    {
        return new EnvironmentInspector()->inspect($this->app);
    }

    public function providers(): array
    {
        return new ProvidersInspector()->inspect($this->app);
    }

    public function aliases(): array
    {
        return new AliasesInspector()->inspect($this->app);
    }

    public function bindings(): array
    {
        return new BindingsInspector()->inspect($this->app);
    }

    public function instances(): array
    {
        return new InstancesInspector()->inspect($this->app);
    }

    public function extenders(): array
    {
        return new ExtendersInspector()->inspect($this->app);
    }

    public function router(): array
    {
        return new RouterInspector()->inspect($this->app);
    }

    public function inspect(): Collection
    {
        return new Collection([
            'environment' => $this->environment(),
            'providers' => $this->providers(),
            'aliases' => collect($this->aliases())->sortKeys(),
            'bindings' => collect($this->bindings())->sortKeys(),
            'instances' => collect($this->instances())->sortKeys(),
            'extenders' => collect($this->extenders())->sortKeys(),
            'router' => $this->router(),
        ]);
    }

    public function toArray(): array
    {
        return $this->inspect()->toArray();
    }
}
