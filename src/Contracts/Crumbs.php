<?php

/**
 * Crumbs interface.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Contracts;

/**
 * Crumbs is meant for storing a registry of `Crumb` classes.
 */
interface Crumbs
{
	/**
	 * Add a crumb.
	 *
	 * @param class-string<Crumb> $crumb
	 */
	public function add(string $name, string $crumb): void;

	/**
	 * Removes a crumb.
	 */
	public function remove(string $name): void;

	/**
	 * Checks if a crumb is registered.
	 */
	public function has(string $name): bool;

	/**
	 * Resolves and returns a crumb object or `null`.
	 */
	public function get(string $name, array $params = []): ?Crumb;
}
