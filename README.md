# laravel-overseer

Runtime introspection of the Laravel application container, router, and environment.

## Requirements

- PHP 8.4+
- Laravel 13+

## Installation

```bash
composer require deplox/laravel-overseer
```

`OverseerServiceProvider` and the `Overseer` facade alias are auto-discovered via `composer.json`.

## Usage

```php
use Deplox\Overseer\Facades\Overseer;

Overseer::environment();   // PHP, Laravel, Composer, and DB versions
Overseer::providers();     // registered/deferred providers + what each provides
Overseer::aliases();       // container alias map
Overseer::bindings();      // bindings with resolved/singleton flags
Overseer::instances();     // cached singleton instances
Overseer::extenders();     // extend() callbacks per abstract
Overseer::router();        // all routes with method, URI, action, and middleware
Overseer::inspect();       // all of the above as a Collection
Overseer::toArray();       // same, as a plain array
```

Or resolve the manager directly:

```php
$overseer = app(Deplox\Overseer\OverseerManager::class);
$overseer->inspect();
```

## Inspectors

Each inspector takes the `Application` instance and returns an array:

| Inspector | Returns |
|---|---|
| `EnvironmentInspector` | PHP, Laravel, Composer, and database versions |
| `ProvidersInspector` | Loaded and deferred providers with their provided bindings |
| `AliasesInspector` | The `abstractAliases` map from the container |
| `BindingsInspector` | Every binding with `resolved` and `singleton` flags |
| `InstancesInspector` | Cached singleton instances, objects resolved to their class name |
| `ExtendersInspector` | Count of `extend()` callbacks registered per abstract |
| `RouterInspector` | Routes (normalised closures, expanded middleware) and middleware groups |

## Where this pays off

- Diagnostic endpoints (`GET /admin/system`)
- Architecture tests (`expect(Overseer::providers())->toHaveKey(...)`)
- Debugging "why is my container resolving X?" without `dd($app)` recursion blow-ups
- Generated admin panels that reflect the live application state

## License

MIT — see [LICENSE](LICENSE).
