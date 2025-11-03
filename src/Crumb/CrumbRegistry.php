<?php

/**
 * Crumb types registry.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb;

use TypeError;

/**
 * Registry for storing crumb classes.
 */
final class CrumbRegistry
{
	/**
	 * Stores the array of crumb classes.
	 */
	protected array $crumbs = [];

	/**
	 * Allows registering a default set of crumb classes.
	 */
	public function __construct(array $crumbs = [])
	{
		foreach ($crumbs as $key => $className) {
			$this->register($key, $className);
		}
	}

	/**
	 * Add a crumb class.
	 *
	 * @param class-string<Crumb> $className
	 */
	public function register(string $key, string $className): void
	{
		if (! is_subclass_of($className, Crumb::class)) {
			throw new TypeError(esc_html(sprintf(
				// Translators: %s is a PHP class name.
				__('Only %s classes can be registered', 'x3p0-breadcrumbs'),
				Crumb::class
			)));
		}

		$this->crumbs[$key] = $className;
	}

	/**
	 * Removes a crumb class.
	 */
	public function unregister(string $key): void
	{
		unset($this->crumbs[$key]);
	}

	/**
	 * Checks if a crumb class is registered.
	 */
	public function isRegistered(string $key): bool
	{
		return isset($this->crumbs[$key]);
	}

	/**
	 * Returns a crumb type.
	 *
	 * @return null|class-string<Crumb>
	 */
	public function get(string $key): ?string
	{
		return $this->isRegistered($key) ? $this->crumbs[$key] : null;
	}
}
