<?php

/**
 * Taxonomy query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query;

use WP_Term;
use X3P0\Breadcrumbs\Contracts\Breadcrumbs;

class Tax extends Query
{
	/**
	 * {@inheritdoc}
	 */
	public function __construct(
		protected Breadcrumbs $breadcrumbs,
		protected ?WP_Term $term = null
	) {}

	/**
	 * {@inheritdoc}
	 */
	public function make(): void
	{
		$term = $this->term ?: get_queried_object();

		$this->breadcrumbs->assemble('home');
		$this->breadcrumbs->assemble('term', [ 'term' => $term ]);
		$this->breadcrumbs->assemble('paged');
	}
}
