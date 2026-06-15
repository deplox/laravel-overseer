# laravel-overseer

[![Tests](https://github.com/deplox/laravel-overseer/actions/workflows/tests.yml/badge.svg)](https://github.com/deplox/laravel-overseer/actions/workflows/tests.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/deplox/laravel-overseer.svg)](https://packagist.org/packages/deplox/laravel-overseer)
[![PHP Version](https://img.shields.io/badge/php-%5E8.4-blue)](https://www.php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

Runtime introspection of the Laravel application container, router, and environment.

---

## Requirements

- PHP 8.4+
- Laravel 13+

## Installation

```bash
composer require deplox/laravel-overseer
```

`OverseerServiceProvider` and the `Overseer` facade are auto-discovered via `composer.json` — no manual registration required.

---

## Usage

```php
use Deplox\Overseer\Facades\Overseer;

Overseer::environment();   // PHP, Laravel, Composer, and DB versions
Overseer::providers();     // all registered and deferred service providers
Overseer::aliases();       // container alias map (short name → class names)
Overseer::bindings();      // every binding with resolved/singleton flags
Overseer::instances();     // cached singleton instances, objects as class names
Overseer::extenders();     // extend() callback counts per abstract
Overseer::router();        // all routes with method, URI, middleware, and action
Overseer::inspect();       // all of the above as a Collection
Overseer::toArray();       // same, as a plain nested array
```

Or resolve the manager directly:

```php
$overseer = app(Deplox\Overseer\OverseerManager::class);
$result   = $overseer->inspect();
```

---

## Inspectors

Each inspector implements `Deplox\Overseer\Contracts\Inspector` and can be used standalone.

| Inspector | Return shape |
|---|---|
| `EnvironmentInspector` | `array{php: string, laravel: string, composer: string, database: string}` |
| `ProvidersInspector` | `array<class-string, array{loaded: bool, deferred: bool, provides: string[]}>` |
| `AliasesInspector` | `array<string, string[]>` — short name → bound class names |
| `BindingsInspector` | `array<class-string, array{resolved: bool, singleton: bool}>` |
| `InstancesInspector` | `array<string, string>` — abstract → class name or debug type |
| `ExtendersInspector` | `array<string, int>` — abstract → number of extenders |
| `RouterInspector` | `array{routes: array<string, array<string, mixed>>, middlewares: array{groups: ..., aliases: ..., priority: ...}}` |

### Sample output

**`Overseer::environment()`**

```php
[
    'php'      => '8.4.2',
    'laravel'  => '13.0.0',
    'composer' => '2.8.4',
    'database' => '8.0.32',   // or 'sqlite', or '-' if DB is unreachable
]
```

**`Overseer::bindings()`** (excerpt)

```php
[
    'Illuminate\Cache\CacheManager' => ['resolved' => true,  'singleton' => true],
    'Illuminate\Log\LogManager'     => ['resolved' => false, 'singleton' => true],
    'some.transient'                => ['resolved' => false, 'singleton' => false],
]
```

**`Overseer::providers()`** (excerpt)

```php
[
    'Illuminate\Cache\CacheServiceProvider' => [
        'loaded'   => true,
        'deferred' => false,
        'provides' => [],
    ],
    'Illuminate\Auth\AuthServiceProvider' => [
        'loaded'   => false,
        'deferred' => true,
        'provides' => ['auth', 'auth.driver'],
    ],
]
```

**`Overseer::router()`** (excerpt)

```php
[
    'routes' => [
        'api/users' => [
            'GET|HEAD' => [
                'name'          => 'api.users.index',
                'method'        => 'GET|HEAD',
                'uri'           => 'api/users',
                'middleware'    => ['api', 'auth:sanctum'],
                'action'        => ['uses' => 'App\Http\Controllers\UserController@index'],
                'fallback'      => false,
                'defaults'      => [],
                'wheres'        => [],
                'bindingFields' => [],
                'lockSeconds'   => null,
                'waitSeconds'   => null,
                'withTrashed'   => false,
            ],
        ],
    ],
    'middlewares' => [
        'groups'   => ['web' => [...], 'api' => [...]],
        'aliases'  => ['auth' => 'Illuminate\Auth\Middleware\Authenticate', ...],
        'priority' => [...],
    ],
]
```

---

## Where this pays off

- **Diagnostic endpoints** — expose a `GET /admin/system` route that returns `Overseer::inspect()->toArray()`.
- **Architecture tests** — assert your provider list, binding shapes, or route structure in Pest:
  ```php
  expect(Overseer::providers())->toHaveKey(App\Providers\AppServiceProvider::class);
  expect(Overseer::bindings()['cache']['singleton'])->toBeTrue();
  ```
- **Debugging resolution problems** — check `bindings`, `instances`, and `extenders` without `dd($app)` blowing up your terminal.
- **Generated admin UIs** — feed `router()` to a table component that reflects live route registration.

---

## Extending

Every inspector is `final` and receives the `Application` instance — no configuration required. You can call them directly without going through the `OverseerManager`:

```php
use Deplox\Overseer\Inspectors\RouterInspector;

$routes = (new RouterInspector)->inspect(app());
```

To add your own inspector, implement `Deplox\Overseer\Contracts\Inspector`:

```php
use Deplox\Overseer\Contracts\Inspector;
use Illuminate\Contracts\Foundation\Application;

final class QueueInspector implements Inspector
{
    public function inspect(Application $app): array
    {
        return [
            'default'     => config('queue.default'),
            'connections' => array_keys(config('queue.connections', [])),
        ];
    }
}
```

---

## Contributing

Bug reports, feature requests, and pull requests are welcome. Please open an issue before submitting a large PR so we can discuss the approach.

```bash
composer install
composer test   # Pest test suite
composer stan   # PHPStan (level 8, Larastan extensions)
```

---

## License

MIT — see [LICENSE](LICENSE).
