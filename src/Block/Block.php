<?php

/**
 * Block interface.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Block;

use WP_Block;

/**
 * Contract for server-rendered block types. Implementations receive the
 * block's saved attributes, inner content, and `WP_Block` instance and
 * return the final front-end markup.
 */
interface Block
{
	/**
	 * Renders the block and returns its HTML for output on the front end.
	 */
	public function render(array $attributes, string $content, WP_Block $block): string;
}
