# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.1.0] - 2025-10-05

### Added

- New separator color support for `theme.json` for the block. This can be set via `settings.custom.x3p0/breadcrumbs.color.separator`.

### Fixed

- When using the home icon option for the block, the spacing for it and the home label now inherit the `gap` (block spacing) setting, which was added in v.3.0.0.
- The `Markup/Html` class correctly renders the HTML attributes on crumbs. Previously, the attributes were malformed.

## [3.0.0] - 2025-09-26

### Added

- `spacing.blockGap` support was added, which now lets theme authors and users control the spacing between breadcrumb items via the standard block gap/spacing design tools.
  - WordPress 6.9: Theme authors should switch to styling this via `styles.blocks.x3p0/breadcrumbs.spacing.blockGap` (as of the current release date, this works with the Gutenberg plugin enabled).
  - WordPress 6.8: `blockGap` styling doesn't work in `theme.json`, so this is needed in the `css` field for the block: `&.is-layout-flex { gap: var(--wp--preset--spacing--20); }`
  - The old `settings.x3p0/breadcrumbs.blockGap` configuration will still work.
  - The fallback `blockGap` style for the block is `0.5rem`.
- Horizontal flex `layout` support was added, primarily as a requirement for `blockGap` to work. This is not configurable, but it does use the WordPress layout implementation instead of custom CSS.

### Fixed

- Corrected handling of the option for showing breadcrumbs on the front page. Previously, they did not appear if enabled.
- Editor scripts translations should now work. Previously, a call to `wp_set_script_translations()` was missing.
- The `Container` interface previously incorrectly extended the `Bootable` interface.
- Accessibility: The `aria-current` attribute is now applied to the last breadcrumb item.

### Changed

- The plugin now only supports WordPress 6.8. Any back-compat code for older versions has been removed.
- Interactivity router region support moved to the `container_attr` array for `Markup` classes. These attributes are no longer forcefully injected and can be overwritten.
- The `breadcrumbs` value is now applied to the `data-wp-router-region` attribute.
- When filtering `x3p0/breadcrumbs/builder/pre/build` a type error will be thrown if a non-null value other than `X3P0\Breadcrumbs\Contracts\Builder` implementation is returned.

### Removed

- The `Plugin` class was removed and renamed to `App`. This should not affect old installations since third-party devs should have been using the `plugin()` helper function.
- The `Trail` class has been removed, which was deprecated in version 2.0.0.


## [2.1.0] - 2025-08-15

### Added

- New support for the WordPress `wp_register_block_types_from_metadata_collection()` function, which simplifies block registration in WP 6.8+. Currently, 6.6 and 6.7 registration methods are supported but will likely be removed in the next version.

### Changed

- Removed an unnecessary import of the `DirectoryIterator` class.

### Fixed

- Corrected a fatal error in rare circumstances where posts have ancestors of a post type that is no longer registered (e.g., an attachment uploaded to a product). The code now checks that a post type exists before attempting to grab a post object.
- The block now supports interactive regions, which correctly adds the page 2, 3, and so on crumbs when a user has enhanced pagination (client-side navigation) enabled for the Query Loop block. Note that this only works if the Breadcrumbs block already appear on the initial page.

## [2.0.1] - 2024-10-31

### Changed

- The `Assembler\PostType` and `Crumb\PostType` classes both now have a `$type` property that can be set when initializing the classes. Previously, these were mismatched, and one was named `$post_type`.

### Fixed

- A call to the `Crumb\PostType` class used the wrong parameter name of `$post_type` instead of `$type`.

## [2.0.0] - 2024-10-21

### New

- **Block:** Added an option for removing the first breadcrumb.
- **Block:** Added a "Markup Style" option for selecting between plain HTML, Microdata, and RDFa (default) markup.
- Overhauled the plugin to use a more robust OOP structure that will allow it to be extended.
- Several new hooks that act as extension points for third-party developers:
	- `x3p0/breadcrumbs/environment`
	- `x3p0/breadcrumbs/builder/pre/build`
	- `x3p0/breadcrumbs/builder/config`
	- `x3p0/breadcrumbs/markup/config`

### Deprecated

- The `X3P0\Breadcrumbs\Trail` static class should no longer be used and will be removed in version 3.0.0. Instead, use the existing `Environment`, `Builder`, and `Markup` implementations to build breadcrumbs or the **Breadcrumbs** block.

### Removed

- The PHP APIs are no longer backward compatible with version 1.0.x.
- Setting the HTML structure via options is no longer supported. Instead, use a `Markup` class.

## [1.0.1] - 2023-10-25

### Changed

- Block API bumped to version 3.

### Removed

- Removed the `x3p0/breadcrumbs/trail` filter hook at the request of a reviewer for the WordPress.org Plugin Review Team.

## [1.0.0] - 2023-07-15

### Added

- 🎉 Literally everything. This is version 1.0, after all.
