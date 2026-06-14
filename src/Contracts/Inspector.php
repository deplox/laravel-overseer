<?php

declare(strict_types=1);

namespace Deplox\Overseer\Contracts;

use Illuminate\Contracts\Foundation\Application;

interface Inspector
{
    /**
     * @return array<array-key, mixed>
     */
    public function inspect(Application $app): array;
}
