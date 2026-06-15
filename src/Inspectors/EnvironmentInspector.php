<?php

declare(strict_types=1);

namespace Deplox\Overseer\Inspectors;

use Deplox\Overseer\Contracts\Inspector;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;
use Throwable;

final class EnvironmentInspector implements Inspector
{
    /**
     * @return array{php: string, laravel: string, composer: string, database: string}
     */
    public function inspect(Application $app): array
    {
        /** @var \Illuminate\Support\Composer */
        $composer = $app->make(\Illuminate\Support\Composer::class);

        return [
            'php' => PHP_VERSION,
            'laravel' => $app->version(),
            'composer' => $composer->getVersion() ?? '-',
            'database' => $this->databaseVersion($app),
        ];
    }

    /**
     * Best-effort database version probe.
     *
     * Tries `select version()` (works on MySQL/MariaDB/Postgres) and falls back
     * to the connection driver name when the query is unsupported (e.g. SQLite)
     * or the connection is unconfigured.
     */
    private function databaseVersion(Application $app): string
    {
        try {
            $resolver = $app->make(\Illuminate\Database\ConnectionResolverInterface::class);
        } catch (Throwable) {
            return '-';
        }

        try {
            $result = $resolver->connection()->select('select version() as version');
            $version = $result[0]->version ?? null;

            if (is_string($version)) {
                return Str::before($version, ' (');
            }
        } catch (Throwable) {
            // Driver doesn't support `select version()` (e.g. sqlite) or DB unreachable.
        }

        try {
            return $resolver->connection()->getDriverName();
        } catch (Throwable) {
            return '-';
        }
    }
}
