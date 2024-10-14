<?php

/**
 * The helpers functions file houses any necessary PHP functions for the plugin.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

use X3P0\Breadcrumbs\Contracts\Bootable;

/**
 * Bootstraps the plugin.
 */
function boot(): void
{
	plugin();
}

/**
 * Mini container used to reference the various plugin components. Bootstraps
 * the plugin on first call by executing each component's `boot()` method. The
 * `plugin()` function acts as the single instance of the plugin, and devs can
 * access any class/component by passing in its reference via the `$component`
 * parameter (useful for accessing hooks within classes).
 */
function plugin(string $component = ''): mixed
{
	static $bindings = [];

	// If there are no bound components, register and boot them.
	if ([] === $bindings) {
		// Bind instances of the plugin's component classes that need to
		// be booted when the plugin launches.
		$bindings = [
			'block' => new Block(untrailingslashit(__DIR__ . '/..'))
		];

		// Boot each of the components.
		foreach ($bindings as $binding) {
			$binding instanceof Bootable && $binding->boot();
		}
	}

	return '' === $component ? $bindings : $bindings[ $component ];
}
