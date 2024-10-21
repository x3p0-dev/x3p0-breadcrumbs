# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2024-10-20

### Changed

- The PHP APIs are no longer backward compatible with version 1.0.x.
- Overhauled the plugin to use a more robust OOP structure that will allow it to be extended.

### Deprecated

- The `X3P0\Breadcrumbs\Trail` static class should no longer be used and will be removed in version 3.0.0. Instead, use the existing `Environment`, `Builder`, and `Markup` implementations to build breadcrumbs or the **Breadcrumbs** block.

## [1.0.1] - 2023-10-25

### Changed

- Block API bumped to version 3.

### Removed

- Removed the `x3p0/breadcrumbs/trail` filter hook at the request of a reviewer for the WordPress.org Plugin Review Team.

## [1.0.0] - 2023-07-15

### Added

- 🎉 Literally everything. This is version 1.0, after all.
