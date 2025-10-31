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

# Prevent direct access.
defined('ABSPATH') || exit;

use X3P0\Breadcrumbs\Block\Type\Breadcrumbs;

/**
 * @global array $attributes
 */
echo (new Breadcrumbs($attributes))->render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
