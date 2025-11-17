<?php

/**
 * PhpStorm metadata, particularly for the service container.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare (strict_types = 1);

namespace PHPSTORM_META
{
	// For get() method.
	override(\X3P0\Breadcrumbs\Framework\Core\Container::get(0), map(['' => '@']));

	// For make() method.
	override(\X3P0\Breadcrumbs\Framework\Core\Container::make(0), map(['' => '@']));
}
