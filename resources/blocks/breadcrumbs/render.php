<?php

/**
 * Block render.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

# Prevent direct access.
defined('ABSPATH') || exit;

use WP_Block;
use X3P0\Breadcrumbs\Block\Renderer\Breadcrumbs;

/**
 * @var array    $attributes Block attributes.
 * @var string   $content    The block content.
 * @var WP_Block $block      Block instance.
 */
// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
echo container()->get(Breadcrumbs::class)->render(
	attributes: $attributes,
	content:    $content,
	block:      $block
);
// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
