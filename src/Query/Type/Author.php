<?php

/**
 * Author query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query\Type;

use WP_Rewrite;
use WP_User;
use X3P0\Breadcrumbs\Contracts\Builder;
use X3P0\Breadcrumbs\Query\AbstractQuery;

class Author extends AbstractQuery
{
	/**
	 * {@inheritdoc}
	 */
	public function __construct(
		protected Builder $builder,
		protected ?WP_User $user = null
	) {
		parent::__construct($this->builder);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @global WP_Rewrite $GLOBALS['wp_rewrite']
	 */
	public function query(): void
	{
		$user = $this->user ?: new WP_User(get_query_var('author'));

		$this->builder->assemble('home');
		$this->builder->assemble('rewrite-front');

		// If $author_base exists, check for parent pages.
		if (! empty($GLOBALS['wp_rewrite']->author_base)) {
			$this->builder->assemble('path', [
				'path' => $GLOBALS['wp_rewrite']->author_base
			]);
		}

		$this->builder->addCrumb('author', [ 'user' => $user ]);

		// If viewing an author search, add the search crumb. This
		// handles URLs like `/?s={search}&author_name={name}`.
		if (is_search()) {
			$this->builder->addCrumb('search');
		}

		$this->builder->assemble('paged');
	}
}
