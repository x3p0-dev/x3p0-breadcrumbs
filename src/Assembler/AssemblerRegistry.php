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

use TypeError;

/**
 * Registry class for storing assembler classes.
 */
final class AssemblerRegistry
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
			throw new TypeError(esc_html(sprintf(
				// Translators: %s is a PHP class name.
				__('Only %s classes can be registered', 'x3p0-breadcrumbs'),
				Assembler::class
			)));
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
		return isset($this->assemblers[$key]);
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
