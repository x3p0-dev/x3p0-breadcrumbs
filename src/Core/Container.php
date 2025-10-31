<?php

/**
 * Container interface.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Core;

use Closure;

interface Container
{
	/**
	 * Bind an abstract to a concrete implementation.
	 */
	public function bind(string $abstract, Closure|string|null $concrete = null, bool $shared = false): void;

	/**
	 * Register a transient service (new instance each time).
	 */
	public function transient(string $abstract, Closure|string|null $concrete = null): void;

	/**
	 * Bind a singleton (cached instance).
	 */
	public function singleton(string $abstract, Closure|string|null $concrete = null): void;

	/**
	 * Register an existing instance as a singleton.
	 */
	public function instance(string $abstract, object $instance): void;

	/**
	 * Resolve a binding from the container.
	 */
	public function get(string $abstract, array $parameters = []): mixed;

	/**
	 * Instantiate a concrete instance.
	 */
	public function make(string $abstract, array $parameters = []): object;

	/**
	 * Check if an abstract is bound.
	 */
	public function has(string $abstract): bool;

	/**
	 * Check if an abstract is bound as a singleton.
	 */
	public function isShared(string $abstract): bool;

	/**
	 * Determine if the given concrete is buildable.
	 */
	public function isBuildable(mixed $concrete): bool;
}
