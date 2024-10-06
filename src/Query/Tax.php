<?php

/**
 * Taxonomy query class.
 *
 * Called to build breadcrumbs on taxonomy (term) archive pages.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Query;

use WP_Term;
use X3P0\Breadcrumbs\Contracts\Breadcrumbs;

class Tax extends Base
{
	public function __construct(
		protected Breadcrumbs $breadcrumbs,
		protected ?WP_Term $term = null
	) {}

	public function make(): void
	{
		$term = $this->term ?: get_queried_object();

		$this->breadcrumbs->build('home');
		$this->breadcrumbs->build('term', [ 'term' => $term ]);
		$this->breadcrumbs->crumb('term', [ 'term' => $term ]);
		$this->breadcrumbs->build('paged');
	}
}
