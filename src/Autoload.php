<?php

/**
 * Autoloader.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

/**
 * A simple PSR-4-compliant autoloader for loading the plugin's classes.
 *
 * @link https://www.php-fig.org/psr/psr-4/
 */
class Autoload
{
	/**
	 * Register the autoloader.
	 */
	public static function register(): bool
	{
		return spl_autoload_register([ __CLASS__, 'autoload' ], true, true);
	}

	/**
	 * Autoloads class if it's in the theme's namespace.
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
