<?php

declare(strict_types=1);

namespace Deplox\Overseer;

use Illuminate\Support\ServiceProvider;
use Override;

final class OverseerServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        $this->app->singleton('overseer', fn ($app): OverseerManager => new OverseerManager($app));
    }
}
