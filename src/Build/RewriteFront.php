<?php

/**
 * Rewrite front build class.
 *
 * Adds the rewrite front path crumbs if a rewrite front is set. The rewrite
 * front is determined based on the base post permalink structure. For example,
 * `/archives/%postname%` will give you a rewrite front of `archives`. Even
 * though this is set for the post permalink structure, archives, other post
 * types, and taxonomies may still use it.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Build;

class RewriteFront extends Base
{
	/**
	 * {@inheritdoc}
	 *
	 * @global WP_Rewrite $GLOBALS['wp_rewrite']
	 */
	public function make(): void
	{
		if ($GLOBALS['wp_rewrite']->front) {
			$this->breadcrumbs->build('path', [
				'path' => $GLOBALS['wp_rewrite']->front
			]);
		}
	}
}
