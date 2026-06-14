<?php

declare(strict_types=1);

namespace Deplox\Overseer\Contracts;

use Illuminate\Contracts\Foundation\Application;

interface Inspector
{
    public function inspect(Application $app): array;
}
