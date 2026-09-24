<?php

/**
 * Icon preset definition interface.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Icon;

use UnitEnum;

/**
 * Contract for an enum case that names the icon preset key it resolves
 * through, so the icon system can accept it wherever a key is expected.
 * {@see IconPresetKey} implements it for this plugin's own keys, and anywhere
 * a key is accepted the type is `IconPresetDefinition|string`.
 */
interface IconPresetDefinition extends UnitEnum
{
	/**
	 * Returns the icon preset key this case resolves through.
	 */
	public function presetKey(): string;
}
