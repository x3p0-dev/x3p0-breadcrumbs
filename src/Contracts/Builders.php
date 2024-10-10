<?php

/**
 * Builders interface.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Contracts;

/**
 * Builders is meant for storing a registry of `Builder` classes.
 */
interface Builders
{
	/**
	 * Adds a builder.
	 *
	 * @param class-string<Builder> $builder
	 */
	public function add(string $name, string $builder): void;

	/**
	 * Removes a builder.
	 */
	public function remove(string $name): void;

	/**
	 * Checks if a builder is registered.
	 */
	public function has(string $name): bool;

	/**
	 * Returns a builder class name or `null`.
	 *
	 * @return null|class-string<Builder>
	 */
	public function get(string $name): ?string;

	/**
	 * Resolves a builder implementation.
	 */
	public function resolve(string $name, array $params = []): ?Builder;
}
