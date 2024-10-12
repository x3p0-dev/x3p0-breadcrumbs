<?php

/**
 * Assemblers interface.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Contracts;

/**
 * Assemblers are meant for storing a registry of `Assembler` classes.
 */
interface Assemblers
{
	/**
	 * Adds an assembler.
	 *
	 * @param class-string<Assembler> $assembler
	 */
	public function add(string $name, string $assembler): void;

	/**
	 * Removes an assembler.
	 */
	public function remove(string $name): void;

	/**
	 * Checks if an assembler is registered.
	 */
	public function has(string $name): bool;

	/**
	 * Resolves and returns an assembler object or `null`.
	 */
	public function get(string $name, array $params = []): ?Assembler;
}
