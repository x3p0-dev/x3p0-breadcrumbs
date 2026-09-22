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
 * Wires the Icon subsystem into the container.
 */
final class IconServiceProvider extends ServiceProvider
{
	/**
	 * Shared per request, so every consumer gets the same instances.
	 *
	 * @var  array<int|string, string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const SINGLETONS = [
		IconRenderer::class,
		IconOptionRegistry::class,
		IconOptionGroupRegistry::class
	];

	/**
	 * Booted so the plugin's icons are registered before anything fetches one,
	 * and the built-in options seeded before anything resolves or lists them.
	 *
	 * @var  array<int|string, string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const BOOTABLE = [
		IconRegistrar::class,
		IconOptionRegistrar::class
	];
}
