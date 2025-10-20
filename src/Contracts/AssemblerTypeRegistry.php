<?php

/**
 * Assembler type registry interface.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Contracts;

/**
 * Stores a registry of assembler types.
 */
interface AssemblerTypeRegistry
{
	/**
	 * Adds an assembler type.
	 *
	 * @param class-string<Assembler> $type
	 */
	public function add(string $name, string $type): void;

	/**
	 * Removes an assembler type.
	 */
	public function remove(string $name): void;

	/**
	 * Checks if an assembler type is registered.
	 */
	public function has(string $name): bool;

	/**
	 * Returns and assembler class or `null`.
	 *
	 * @return null|class-string<Assembler>
	 */
	public function get(string $name): ?string;
}
