<?php

/**
 * Container interface.
 *
 * @version   1.0.0
 * @package   X3P0\Framework
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Framework\Core;

/**
 * Defines the dependency injection container interface, which allows for
 * binding concrete implementations to abstracts. The container supports
 * transient, singleton, and single-instance bindings.
 */
interface Container
{
	/**
	 * Bind an abstract to a concrete implementation.
	 */
	public function bind(string $abstract, mixed $concrete = null, bool $shared = false): void;

	/**
	 * Register a transient service (new instance each time).
	 */
	public function transient(string $abstract, mixed $concrete = null): void;

	/**
	 * Register a singleton service (cached instance).
	 */
	public function singleton(string $abstract, mixed $concrete = null): void;

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
}
