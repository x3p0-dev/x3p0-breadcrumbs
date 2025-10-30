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
 * Registry class for storing crumb types.
 */
final class CrumbRegistry
{
	/**
	 * Stores the array of crumb type classes.
	 */
	protected array $types = [];

	/**
	 * Allows registering a default set of crumbs.
	 */
	public function __construct(array $types = [])
	{
		foreach ($types as $type => $className) {
			$this->register($type, $className);
		}
	}

	/**
	 * Add a crumb type.
	 *
	 * @param class-string<Crumb> $className
	 */
	public function register(string $type, string $className): void
	{
		if (! is_subclass_of($className, Crumb::class)) {
			throw new TypeError(esc_html(sprintf(
				// Translators: %s is a PHP class name.
				__('Only %s classes can be registered', 'x3p0-breadcrumbs'),
				Crumb::class
			)));
		}

		$this->types[$type] = $className;
	}

	/**
	 * Removes a crumb type.
	 */
	public function unregister(string $type): void
	{
		unset($this->types[$type]);
	}

	/**
	 * Checks if a crumb type is registered.
	 */
	public function isRegistered(string $type): bool
	{
		return isset($this->types[$type]);
	}

	/**
	 * Returns a crumb type.
	 *
	 * @return null|class-string<Crumb> $type
	 */
	public function get(string $type): ?string
	{
		return $this->isRegistered($type) ? $this->types[$type] : null;
	}

	/**
	 * Gets the registered type for a given class name.
	 */
	public function getTypeByClassName(string $className): ?string
	{
		$type = array_search($className, $this->types, true);
		return $type !== false ? $type : null;
	}

	/**
	 * @deprecated 4.0.0
	 */
	public function add(string $type, string $className): void
	{
		$this->register($type, $className);
	}

	/**
	 * @deprecated 4.0.0
	 */
	public function remove(string $type): void
	{
		$this->unregister($type);
	}

	/**
	 * @deprecated 4.0.0
	 */
	public function has(string $type): bool
	{
		return $this->isRegistered($type);
	}
}
