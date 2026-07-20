<?php

/**
 * Enum definition interface.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Contracts;

use BackedEnum;

/**
 * Contract for a backed enum case that declaratively maps itself to a
 * concrete class, so a factory can build and enumerate its subsystem's
 * registered types without depending on the concrete enum.
 *
 * @internal The interface itself is an internal contract; third-party code
 *           should not use it directly.
 */
interface EnumDefinition extends BackedEnum
{
	/**
	 * Returns the concrete class associated with this case.
	 *
	 * @return class-string
	 */
	public function className(): string;
}
