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

/**
 * Bootstraps the plugin.
 */
function boot(): void
{
	plugin()->boot();
}

/**
 * Stores the single instance of the plugin in the static `$plugin` variable.
 * Devs can access any class/component by passing in its reference via the
 * `$abstract` parameter (useful for accessing hooks within classes).
 *
 * @since 1.0.0
 */
function plugin(string $abstract = ''): mixed
{
	static $plugin;

	if (! $plugin instanceof Plugin) {
		do_action('x3p0/breadcrumbs/init', $plugin = new Plugin());
	}

	return '' === $abstract ? $plugin : $plugin->get($abstract);
}
