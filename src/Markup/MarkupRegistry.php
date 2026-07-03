<?php

/**
 * Markup registry.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup;

use X3P0\Breadcrumbs\InvalidTypeException;
use X3P0\Breadcrumbs\Packages\Framework\Contracts\ClassRegistry;

/**
 * Stores `string key => markup class name` mappings that the factory later
 * resolves and instantiates. Registration is guarded so only `Markup`
 * subclasses can be stored.
 */
final class MarkupRegistry implements ClassRegistry
{
	/**
	 * Maps each registered key to its markup class name.
	 */
	private array $markups = [];

	/**
	 * Optionally seeds the registry with an initial set of key => class
	 * mappings.
	 */
	public function __construct(array $markups = [])
	{
		foreach ($markups as $key => $className) {
			$this->register($key, $className);
		}
	}

	/**
	 * Registers a markup class under a key, replacing any existing entry. Throws
	 * if the class is not a subclass of `Markup`.
	 *
	 * @param class-string<Markup> $className
	 */
	public function register(string $key, string $className): void
	{
		if (! is_subclass_of($className, Markup::class)) {
			throw InvalidTypeException::notSubclassOf($className, Markup::class);
		}

		$this->markups[$key] = $className;
	}

	/**
	 * Unregisters a markup class.
	 */
	public function unregister(string $key): void
	{
		unset($this->markups[$key]);
	}

	/**
	 * Checks if a markup class is registered.
	 */
	public function isRegistered(string $key): bool
	{
		return array_key_exists($key, $this->markups);
	}

	/**
	 * Returns the markup class name registered under the key, or `null` if none
	 * is registered.
	 *
	 * @return null|class-string<Markup>
	 */
	public function get(string $key): ?string
	{
		return $this->isRegistered($key) ? $this->markups[$key] : null;
	}

	/**
	 * Returns all registered keys mapped to their markup class names. This
	 * is the authoritative list of available markup types, including any
	 * registered by third-party code.
	 *
	 * @return array<string, class-string<Markup>>
	 */
	public function all(): array
	{
		return $this->markups;
	}
}
