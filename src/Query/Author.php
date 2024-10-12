<?php

/**
 * Author query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query;

use WP_Rewrite;
use WP_User;
use X3P0\Breadcrumbs\Contracts\Breadcrumbs;

class Author extends Query
{
	/**
	 * {@inheritdoc}
	 */
	public function __construct(
		protected Breadcrumbs $breadcrumbs,
		protected ?WP_User $user = null
	) {}

	/**
	 * {@inheritdoc}
	 *
	 * @global WP_Rewrite $GLOBALS['wp_rewrite']
	 */
	public function make(): void
	{
		$user = $this->user ?: new WP_User(get_query_var('author'));

		$this->breadcrumbs->build('home');
		$this->breadcrumbs->build('rewrite-front');

		// If $author_base exists, check for parent pages.
		if (! empty($GLOBALS['wp_rewrite']->author_base)) {
			$this->breadcrumbs->build('path', [
				'path' => $GLOBALS['wp_rewrite']->author_base
			]);
		}

		$this->breadcrumbs->crumb('author', [ 'user' => $user ]);
		$this->breadcrumbs->build('paged');
	}
}
