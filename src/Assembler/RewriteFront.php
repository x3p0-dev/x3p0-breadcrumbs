<?php

/**
 * Rewrite front assembler.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler;

use WP_Rewrite;

/**
 * Adds the rewrite front path crumbs if a rewrite front is set. The rewrite
 * front is determined based on the base post permalink structure. For example,
 * `/archives/%postname%` will give you a rewrite front of `archives`. Even
 * though this is set for the post permalink structure, archives, other post
 * types, and taxonomies may still use it.
 */
class RewriteFront extends Assembler
{
	/**
	 * {@inheritdoc}
	 *
	 * @global WP_Rewrite $GLOBALS['wp_rewrite']
	 */
	public function assemble(): void
	{
		if ($GLOBALS['wp_rewrite']->front) {
			$this->builder->assemble('path', [
				'path' => $GLOBALS['wp_rewrite']->front
			]);
		}
	}
}
