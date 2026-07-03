<?php

/**
 * Crumb types registry.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb;

use X3P0\Breadcrumbs\InvalidTypeException;
use X3P0\Breadcrumbs\Packages\Framework\Contracts\ClassRegistry;

/**
 * Stores the `key => class name` mappings for crumb types. New types are added
 * by registering a `Crumb` subclass against a string key, making the crumb
 * subsystem open for extension without touching core files.
 */
final class CrumbRegistry implements ClassRegistry
{
	/**
	 * Maps each crumb type key to its registered class name.
	 *
	 * @var array<string, class-string<Crumb>>
	 */
	private array $crumbs = [];

	/**
	 * Optionally seeds the registry with an initial `key => class` map.
	 */
	public function __construct(array $crumbs = [])
	{
		foreach ($crumbs as $key => $className) {
			$this->register($key, $className);
		}
	}

	/**
	 * Maps a crumb class to a key. Throws when the class is not a `Crumb`
	 * subclass.
	 *
	 * @param class-string<Crumb> $className
	 */
	public function register(string $key, string $className): void
	{
		if (! is_subclass_of($className, Crumb::class)) {
			throw InvalidTypeException::notSubclassOf(esc_html($className), Crumb::class);
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
		return array_key_exists($key, $this->crumbs);
	}

	/**
	 * Returns the class name registered under the key, or null if none.
	 *
	 * @return null|class-string<Crumb>
	 */
	public function get(string $key): ?string
	{
		return $this->isRegistered($key) ? $this->crumbs[$key] : null;
	}
}
