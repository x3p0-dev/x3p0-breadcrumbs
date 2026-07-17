<?php

/**
 * Provides a type case's registry key.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Support;

/**
 * Shared by the backed `*Type` enums (implementing `ClassEnum` and `TypeKey`)
 * to satisfy the `TypeKey` contract: a case's registry key is its backing
 * value. This lets a factory accept a typed reference in place of a bare string
 * key while its lookup stays a single key-based `get()`.
 *
 * @internal This is an internal implementation detail of the plugin, not part
 *           of its public API. Its signature may change or it may be removed at
 *           any time without notice; third-party code should not use it
 *           directly.
 */
trait ProvidesTypeKey
{
	/**
	 * Returns this case's registry key, which is its backing value. Satisfies
	 * the `TypeKey` contract so a case can be passed wherever a key is expected.
	 */
	public function key(): string
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return $this->value;
	}
}
