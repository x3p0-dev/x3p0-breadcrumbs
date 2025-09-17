<?php

/**
 * Plugin lifecycle helper.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-ideas
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

/**
 * A static class that handles the various duties during the plugin's lifecycle.
 */
class Plugin
{
	/**
	 * Bootstraps the plugin and should be used as a callback on the
	 * `plugins_loaded` action hook.
	 */
	public static function boot(): void
	{
		plugin()->boot();
	}
}
