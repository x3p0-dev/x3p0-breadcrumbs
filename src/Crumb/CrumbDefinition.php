<?php

/**
 * Crumb definition interface.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb;

use BackedEnum;

/**
 * Interface required for enums tied to crumbs
 */
interface CrumbDefinition extends BackedEnum
{
	/**
	 * Returns the crumb class associated with the type, mapping each case
	 * to a concrete class under the `Type` sub-namespace.
	 *
	 * @return class-string<Crumb>
	 */
	public function className(): string;
}
