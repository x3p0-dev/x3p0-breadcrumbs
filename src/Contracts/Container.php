<?php

/**
 * Container interface.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Contracts;

/**
 * A simple container used to store and reference the various Plugin components.
 */
interface Container
{
	/**
	 * Registers a single instance of a binding.
	 */
	public function instance(string $abstract, mixed $instance): void;

	/**
	 * Returns a binding or `null`.
	 */
	public function get(string $abstract): mixed;

	/**
	 * Checks if a binding exists.
	 */
	public function has(string $abstract): bool;
}
