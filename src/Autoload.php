<?php

/**
 * Autoloader.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

namespace X3P0\Breadcrumbs;

class Autoload
{
	/**
	 * Register the autoloader.
	 *
	 * @since 1.0.0
	 */
	public static function register(): bool
	{
		return spl_autoload_register([ __CLASS__, 'autoload' ], true, true);
	}

	/**
	 * Autoloads class if it's in the theme's namespace.
	 *
	 * @since 1.0.0
	 */
	public static function autoload(string $class): void
	{
		// Bail if the class is not in our namespace.
		if (0 !== strpos($class, __NAMESPACE__)) {
			return;
		}

		$filename = __DIR__ .  sprintf('/%s.php', str_replace(
			[ __NAMESPACE__ . '\\', '\\' ],
			[ '', DIRECTORY_SEPARATOR ],
			$class
		));

		if (file_exists($filename)) {
			require_once $filename;
		}
	}
}
