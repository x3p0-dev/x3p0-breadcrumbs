<?php

/**
 * Crumb type registry interface.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Contracts;

/**
 * Stores a registry of crumb types.
 */
interface CrumbTypeRegistry
{
	/**
	 * Add a crumb type.
	 *
	 * @param class-string<Crumb> $crumb
	 */
	public function add(string $name, string $crumb): void;

	/**
	 * Removes a crumb type.
	 */
	public function remove(string $name): void;

	/**
	 * Checks if a crumb type is registered.
	 */
	public function has(string $name): bool;

	/**
	 * Returns a crumb type.
	 *
	 * @return null|class-string<Crumb> $crumb
	 */
	public function get(string $name): ?string;
}
