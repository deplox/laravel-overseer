<?php

declare(strict_types=1);

namespace Deplox\Overseer\Inspectors;

use Deplox\Overseer\Contracts\Inspector;
use Illuminate\Contracts\Foundation\Application;

final class ProvidersInspector implements Inspector
{
    /**
     * @return array<string, array{loaded: bool, deferred: bool, provides: string[]}>
     */
    public function inspect(Application $app): array
    {
        $result = [];

        foreach (array_keys($app->getLoadedProviders()) as $class) {
            $provider = $app->getProvider($class);

            if ($provider === null) {
                continue;
            }

            $result[$class] = [
                'loaded' => true,
                'deferred' => $provider->isDeferred(),
                'provides' => $provider->provides(),
            ];
        }

        $loadedClasses = array_keys($result);

        // Group deferred-but-not-yet-loaded providers with the bindings they provide.
        foreach ($app->getDeferredServices() as $binding => $providerClass) {
            if (in_array($providerClass, $loadedClasses, true)) {
                continue;
            }

            if (! isset($result[$providerClass])) {
                $result[$providerClass] = ['loaded' => false, 'deferred' => true, 'provides' => []];
            }

            $result[$providerClass]['provides'][] = $binding;
        }

        return $result;
    }
}
