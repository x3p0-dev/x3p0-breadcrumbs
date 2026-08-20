<?php

/**
 * Icon service provider.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Icon;

use X3P0\Breadcrumbs\Packages\Framework\Core\ServiceProvider;

/**
 * Wires the Icon subsystem into the container. Boots `IconRegistrar`, which
 * registers the plugin's built-in SVGs (see the {@see Icon} enum) with
 * WordPress's icon API on `init`, and `IconOptionRegistrar`, which seeds the
 * `IconOptions` registry late on `init`. Binds `IconResolver` and
 * `IconOptions` as shared singletons so every consumer gets the same
 * instances.
 */
final class IconServiceProvider extends ServiceProvider
{
	/**
	 * Shares single `IconResolver` and `IconOptions` instances per request.
	 *
	 * @var  array<int|string, string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const SINGLETONS = [
		IconResolver::class,
		IconOptions::class
	];

	/**
	 * Boots `IconRegistrar` so the plugin's icons are registered before
	 * anything needs to fetch one via `wp_get_icon()`, and
	 * `IconOptionRegistrar` so the built-in icon options are seeded before
	 * anything resolves or lists them.
	 *
	 * @var  array<int|string, string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const BOOTABLE = [
		IconRegistrar::class,
		IconOptionRegistrar::class
	];
}
