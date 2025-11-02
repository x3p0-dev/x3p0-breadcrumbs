<?php

/**
 * Rewrite front assembler.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler\Type;

use WP_Rewrite;
use X3P0\Breadcrumbs\Assembler\AbstractAssembler;

/**
 * Adds the rewrite front path crumbs if a rewrite front is set. The rewrite
 * front is determined based on the base post permalink structure. For example,
 * `/archives/%postname%` will give you a rewrite front of `archives`. Even
 * though this is set for the post permalink structure, archives, other post
 * types, and taxonomies may still use it.
 */
final class RewriteFront extends AbstractAssembler
{
	/**
	 * @inheritDoc
	 *
	 * @global WP_Rewrite $GLOBALS['wp_rewrite']
	 */
	public function assemble(): void
	{
		if ($GLOBALS['wp_rewrite']->front) {
			$this->context->assemble('path', [
				'path' => $GLOBALS['wp_rewrite']->front
			]);
		}
	}
}
