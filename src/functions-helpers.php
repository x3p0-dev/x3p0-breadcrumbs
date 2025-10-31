<?php

/**
 * The helpers functions file houses any necessary PHP functions for the plugin.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

use X3P0\Breadcrumbs\Core\{Application, Plugin, ServiceContainer};

/**
 * Stores the single instance of the plugin in the static `$plugin` variable.
 * Devs can access any concrete implementation by passing in a reference to its
 * abstract identifier via `plugin()->container()->get($abstract)`.
 */
function plugin(): Application
{
	static $plugin;

	if (! $plugin instanceof Plugin) {
		do_action('x3p0/breadcrumbs/init', $plugin = new Plugin(new ServiceContainer()));
	}

	return $plugin;
}
