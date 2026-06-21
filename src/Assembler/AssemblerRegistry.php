<?php

/**
 * Assembler registry.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler;

use X3P0\Breadcrumbs\Framework\Contracts\ClassRegistry;
use X3P0\Breadcrumbs\InvalidTypeException;

/**
 * Registry class for storing assembler classes.
 */
final class AssemblerRegistry implements ClassRegistry
{
	/**
	 * Stores the array of assembler classes.
	 */
	protected array $assemblers = [];

	/**
	 * Allows registering a default set of assembler classes.
	 */
	public function __construct(array $assemblers = [])
	{
		foreach ($assemblers as $key => $className) {
			$this->register($key, $className);
		}
	}

	/**
	 * Registers an assembler class.
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
	 * Unregisters an assembler class.
	 */
	public function unregister(string $key): void
	{
		unset($this->assemblers[$key]);
	}

	/**
	 * Checks if an assembler is registered.
	 */
	public function isRegistered(string $key): bool
	{
		return array_key_exists($key, $this->assemblers);
	}

	/**
	 * Returns an assembler class or `null`.
	 *
	 * @return null|class-string<Assembler>
	 */
	public function get(string $key): ?string
	{
		return $this->isRegistered($key) ? $this->assemblers[$key] : null;
	}
}
