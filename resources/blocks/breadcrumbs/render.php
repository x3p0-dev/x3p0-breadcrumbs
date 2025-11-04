<?php

/**
 * Block render.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

# Prevent direct access.
defined('ABSPATH') || exit;

use X3P0\Breadcrumbs\Block\Type\Breadcrumbs;

/**
 * @global array $attributes
 */
// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
echo container()->get(Breadcrumbs::class, [
	'attributes' => $attributes
])->render();
// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
