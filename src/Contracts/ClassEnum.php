<?php

/**
 * Class-enum contract.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Contracts;

/**
 * Implemented by the backed enums that name a subsystem's built-in types, so an
 * `EnumRegistrar` can map each case to the concrete class it represents.
 *
 * @internal This is an internal implementation detail of the plugin, not part
 *           of its public API. It may change or be removed at any time without
 *           notice; third-party code should not implement or type-hint against
 *           it directly.
 */
interface ClassEnum
{
	/**
	 * Returns the class name the enum case represents.
	 *
	 * @return class-string
	 */
	public function className(): string;
}
