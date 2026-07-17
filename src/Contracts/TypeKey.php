<?php

/**
 * Type key contract.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Contracts;

/**
 * Implemented by any object that names a registry key, so a factory can accept
 * a typed reference in place of a bare string key. The built-in `*Type` enums
 * implement this (returning their backing value), and third parties may provide
 * their own implementations to have their references resolved the same way.
 */
interface TypeKey
{
	/**
	 * Returns the registry key this reference resolves to.
	 */
	public function key(): string;
}
