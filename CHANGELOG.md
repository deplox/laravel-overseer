# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- `Inspector` contract (`Deplox\Overseer\Contracts\Inspector`) that all inspectors now implement.
- Per-route `middleware` field in `RouterInspector` output — each route entry now includes the middleware stack registered on that route.
- `@method` stubs on the `Overseer` facade for full IDE autocomplete.
- Unit test suites for all seven inspectors and `OverseerManager`.
- `FacadeTest` exercising every `Overseer::*` static method.
- `phpunit.xml` now fails on warnings, deprecations, and notices.
- CI cache key now keyed on `composer.lock` rather than `composer.json`.

### Fixed

- `ProvidersInspector` no longer throws a fatal error when `getProvider()` returns `null` for a loaded provider class (possible under concurrent modification of the provider list).
- `RouterInspector` now uses direct array assignment instead of `Arr::set`, preventing URIs that contain dots (e.g. `api/v1.0/status`) from being incorrectly nested.
- `EnvironmentInspector` uses `PHP_VERSION` (the canonical string) instead of a manually joined tuple.
- `EnvironmentInspector` database version probe now goes through `$resolver->connection()`, making it compatible with connection resolver implementations that don't expose `select` directly on the resolver.

### Changed

- `OverseerManager::inspect()` returns sorted, plain arrays for `aliases`, `bindings`, `instances`, and `extenders` (no nested `Collection` objects survive serialisation).
- `ProvidersInspector` now correctly distinguishes deferred-but-not-yet-loaded providers and groups them with the bindings they provide.

## [0.1.0] - 2026-06-13

### Added

- Initial release with `OverseerManager`, `OverseerServiceProvider`, `Overseer` facade.
- Seven inspectors: `EnvironmentInspector`, `ProvidersInspector`, `AliasesInspector`, `BindingsInspector`, `InstancesInspector`, `ExtendersInspector`, `RouterInspector`.
- Auto-discovery via `composer.json` `extra.laravel`.
- Feature test suite.
