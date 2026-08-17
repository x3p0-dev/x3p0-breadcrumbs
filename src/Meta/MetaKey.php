<?php

/**
 * Meta key enum.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Meta;

/**
 * Enum of post/term meta keys registered by the plugin. Each case's backed
 * string value is the literal meta key stored in the database.
 */
enum MetaKey: string
{
	case Icon = 'x3p0-breadcrumbs-icon';
}
