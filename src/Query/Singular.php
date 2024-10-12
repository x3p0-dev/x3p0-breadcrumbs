<?php

/**
 * Singular query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query;

use WP_Post;
use X3P0\Breadcrumbs\Contracts\Builder;

class Singular extends Query
{
	/**
	 * {@inheritdoc}
	 */
	public function __construct(
		protected Builder $builder,
		protected ?WP_Post $post = null
	) {}

	/**
	 * {@inheritdoc}
	 */
	public function make(): void
	{
		$post = $this->post ?: get_queried_object();

		$this->builder->assemble('home');
		$this->builder->assemble('post', [ 'post' => $post ]);
		$this->builder->assemble('paged');
	}
}
