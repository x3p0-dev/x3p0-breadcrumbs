<?php

/**
 * Markup definition interface.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup;

use BackedEnum;

/**
 * Interface required for enums tied to markup types.
 */
interface MarkupDefinition extends BackedEnum
{
	/**
	 * Returns the markup class associated with the type, mapping each case
	 * to a concrete class under the `Type` sub-namespace.
	 *
	 * @return class-string<Markup>
	 */
	public function className(): string;

	/**
	 * Returns this case's container alias — its key namespaced under the
	 * subsystem as `x3p0/breadcrumbs/markup/{value}` — so the same short key can
	 * be reused across subsystems without colliding in the container's single,
	 * global alias table.
	 */
	public function alias(): string;
}
