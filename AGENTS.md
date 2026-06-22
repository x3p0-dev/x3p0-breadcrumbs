# AGENTS.md

This file provides guidance to agents when working with code in this repository.

## Commands

**PHP linting:**
```bash
./vendor/bin/phpcs -d error_reporting=E_ALL^E_DEPRECATED -s --colors .
./vendor/bin/phpcbf -d error_reporting=E_ALL^E_DEPRECATED -s --colors .
```

**Block editor:**
```bash
npm start      # dev server with watch
npm run build  # production build
```

There is no test suite.

## Architecture Patterns

### Service Providers + DI Container

The plugin bootstraps via a central `Plugin` class that registers service providers. Each subsystem has its own service provider responsible for wiring up its dependencies into the container. Nothing is instantiated until it is needed.

### Registry + Factory + Registrar

Extensible subsystems follow this three-part pattern:

- **Registry** — stores `string key → class name` mappings.
- **Factory** — resolves a key from the registry and instantiates the class via the DI container.
- **Registrar** — a static class whose constants are the canonical string keys and whose `register()` method seeds the registry with the built-in types.

New types are added by registering a class name against a key; the factory handles instantiation. This makes every subsystem open for extension without modifying core files.

### Abstract Base + Final Concrete Types

Each subsystem defines one abstract base class that serves as the contract (typehint against this). Concrete implementations are `final` and live in a `Type/` subdirectory. Abstract classes are never instantiated directly.

### Immutable Configuration Objects

Configuration is passed around as typed objects, not associative arrays. Config classes are immutable, with all properties set at construction time. They also expose a static `fromArray()` factory to allow convenient array-based construction where needed.

### Context Object as Internal API

When a pipeline of collaborating objects (e.g., Query → Assembler → Item) needs shared access to factories, the collection being built, and config, a single **context object** is created at the start and passed through the pipeline. This avoids threading many individual dependencies through every class.

## Coding Conventions

### PHP

- `declare(strict_types=1);` at the top of every file.
- `defined('ABSPATH') || exit;` to block direct access.
- Namespaces follow `Vendor\Plugin\{Subsystem}\{ClassName}`; concrete types are nested under `Type\`.
- Concrete classes are `final`. Abstract classes define the subsystem contract and are the typehint used everywhere.
- Constructor parameters are assigned to `protected readonly` properties — no setters.
- Tabs for indentation (not spaces), per `.phpcs.xml`.
- File-level docblocks include `@author`, `@copyright`, `@license`, `@link`. Method overrides use `@inheritDoc`.

### WordPress

- All output is escaped at the point of output: `esc_html()`, `esc_attr()`, `esc_url()`, etc.
- Hook names use a slash-separated vendor/plugin prefix (e.g., `vendor/plugin/hook-name`).
- Expose a `register` action so third-party code can hook into the plugin lifecycle and access the container to register its own services.
- Global helper functions (`plugin()`, `container()`) provide access to the plugin and DI container without relying on static classes or globals directly.

### Block Editor

- Block source lives under `resources/`; built output goes to `public/`. Never edit built files directly.
- Block attributes map to typed PHP config objects on the server-render side.
- Deprecated block attributes are remapped explicitly rather than left to silently fail.
- Dynamic styles use CSS custom properties scoped to the block's BEM namespace.
