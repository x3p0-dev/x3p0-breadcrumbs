<?php

/**
 * Post type archive query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query\Type;

use WP_Post_Type;
use WP_User;
use X3P0\Breadcrumbs\Contracts\Builder;
use X3P0\Breadcrumbs\Crumb\Type\PostType;
use X3P0\Breadcrumbs\Query\AbstractQuery;

class PostTypeArchive extends AbstractQuery
{
	/**
	 * {@inheritdoc}
	 */
	public function __construct(
		protected Builder $builder,
		protected ?WP_Post_Type $type = null,
		protected ?WP_User $user = null
	) {
		parent::__construct($this->builder);
	}

	/**
	 * {@inheritdoc}
	 */
	public function query(): void
	{
		$type = $this->type ?: get_post_type_object(get_query_var('post_type'));

		$done_post_type = false;

		$this->builder->assemble('home');

		if (false !== $type->rewrite) {
			// Build rewrite front crumbs if post type uses it.
			if ($type->rewrite['with_front']) {
				$this->builder->assemble('rewrite-front');
			}

			// If there's a rewrite slug, check for parents.
			if (! empty($type->rewrite['slug'])) {
				$this->builder->assemble('path', [ 'path' => $type->rewrite['slug'] ]);

				// Check if we've added a post type crumb.
				if ($this->builder->crumbs()->has('post-type')) {
					$done_post_type = true;
				}
			}
		}

		// Add post type crumb.
		if (! $done_post_type) {
			$this->builder->addCrumb('post-type', [ 'type' => $type ]);
		}

		// If viewing a post type archive by author, add author crumb.
		// This handles URLs like `/{type}?=author={author}`.
		if (is_author()) {
			$user = $this->user ?: new WP_User(get_query_var('author'));

			// Add author crumb.
			$this->builder->addCrumb('author', [ 'user' => $user ]);
		}

		// If viewing a post type search, add the search crumb. This
		// handles URLs like `/?s={search}&post_type={type}`.
		if (is_search()) {
			$this->builder->addCrumb('search');
		}

		$this->builder->assemble('paged');
	}
}
