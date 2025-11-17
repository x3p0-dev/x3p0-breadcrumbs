<?php

/**
 * Class registry interface.
 *
 * @version   1.0.0
 * @package   X3P0\Framework
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Framework\Contracts;

/**
 * Defines an interface for creating a registry of class names (not instances),
 * indexed by key.
 */
interface ClassRegistry
{
	/**
	 * Registers a class.
	 *
	 * @param class-string $className
	 */
	public function register(string $key, string $className): void;

	/**
	 * Unregisters a class.
	 */
	public function unregister(string $key): void;

	/**
	 * Checks if a class is registered.
	 */
	public function isRegistered(string $key): bool;

	/**
	 * Returns a class string or `null`.
	 *
	 * @return null|class-string
	 */
	public function get(string $key): ?string;
}
