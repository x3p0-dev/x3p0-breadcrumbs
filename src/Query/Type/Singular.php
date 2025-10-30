<?php

/**
 * Singular query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query\Type;

use WP_Post;
use X3P0\Breadcrumbs\Contracts\Builder;
use X3P0\Breadcrumbs\Query\AbstractQuery;

class Singular extends AbstractQuery
{
	/**
	 * {@inheritdoc}
	 */
	public function __construct(
		protected Builder $builder,
		protected ?WP_Post $post = null
	) {
		parent::__construct($this->builder);
	}

	/**
	 * {@inheritdoc}
	 */
	public function query(): void
	{
		$post = $this->post ?: get_queried_object();

		$this->builder->assemble('home');
		$this->builder->assemble('post', [ 'post' => $post ]);
		$this->builder->assemble('paged');
	}
}
