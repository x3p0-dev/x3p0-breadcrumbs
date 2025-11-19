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

use X3P0\Breadcrumbs\Framework\Core\Application;
use X3P0\Breadcrumbs\Framework\Container\{Container, ServiceContainer};

/**
 * Returns the plugin application, which is stored as a single instance in the
 * static `$plugin` variable.
 */
function plugin(): Application
{
	static $plugin;

	if (! $plugin instanceof Plugin) {
		$plugin = new Plugin(new ServiceContainer());
	}

	return $plugin;
}

/**
 * Helper function for quickly accessing the plugin service container. Devs can
 * access any concrete implementation by passing in a reference to its abstract
 * identifier via `container()->get($abstract)`.
 */
function container(): Container
{
	return plugin()->container();
}

/**
 * Helper function for quickly accessing the breadcrumbs service class and
 * rendering breadcrumbs.
 */
function breadcrumbs(): BreadcrumbsService
{
	return container()->get(BreadcrumbsService::class);
}
