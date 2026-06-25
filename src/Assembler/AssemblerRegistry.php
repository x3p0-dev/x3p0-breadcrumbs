<?php

/**
 * Assembler registry.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler;

use X3P0\Breadcrumbs\Framework\Contracts\ClassRegistry;
use X3P0\Breadcrumbs\InvalidTypeException;

/**
 * Stores the `string key => assembler class name` mappings that the factory
 * resolves against. Registration is validated so that only subclasses of the
 * abstract `Assembler` can be stored.
 */
final class AssemblerRegistry implements ClassRegistry
{
	/**
	 * Maps each registered key to its assembler class name.
	 *
	 * @var array<string, class-string<Assembler>>
	 */
	private array $assemblers = [];

	/**
	 * Registers any key/class-name pairs passed in, allowing the registry
	 * to be seeded with a default set of assemblers at construction.
	 */
	public function __construct(array $assemblers = [])
	{
		foreach ($assemblers as $key => $className) {
			$this->register($key, $className);
		}
	}

	/**
	 * Maps a key to an assembler class name. Throws if the class is not a
	 * subclass of `Assembler`.
	 *
	 * @param class-string<Assembler> $className
	 */
	public function register(string $key, string $className): void
	{
		if (! is_subclass_of($className, Assembler::class)) {
			throw InvalidTypeException::notSubclassOf($className, Assembler::class);
		}

		$this->assemblers[$key] = $className;
	}

	/**
	 * Removes the assembler mapping for the given key, if any.
	 */
	public function unregister(string $key): void
	{
		unset($this->assemblers[$key]);
	}

	/**
	 * Returns whether an assembler is registered under the given key.
	 */
	public function isRegistered(string $key): bool
	{
		return array_key_exists($key, $this->assemblers);
	}

	/**
	 * Returns the assembler class name registered under the key, or `null`
	 * if none is registered.
	 *
	 * @return null|class-string<Assembler>
	 */
	public function get(string $key): ?string
	{
		return $this->isRegistered($key) ? $this->assemblers[$key] : null;
	}
}
