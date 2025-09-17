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
 * Stores the single instance of the app in the static `$app` variable. Devs
 * can access any concrete implementation by passing in a reference to its
 * abstract identifier via `plugin()->get($abstract)`.
 */
function plugin(): App
{
	static $app;

	if (! $app instanceof App) {
		do_action('x3p0/breadcrumbs/init', $app = new App());
	}

	return $app;
}
