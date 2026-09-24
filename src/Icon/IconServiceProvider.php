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
	 * Shared per request, so every consumer gets the same instances — which
	 * matters for `IconPresets` above all, since what an extension registers
	 * on `init` has to be what the trail resolves against and what the editor
	 * lists.
	 *
	 * @var  array<int|string, string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const SINGLETONS = [
		IconRenderer::class,
		IconPresets::class,
		IconOptions::class
	];

	/**
	 * Booted so the plugin's icons are registered before anything fetches
	 * one, and extensions get their moment to register before anything
	 * resolves or lists.
	 *
	 * @var  array<int|string, string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const BOOTABLE = [
		IconRegistrar::class,
		IconPresetRegistrar::class
	];
}
