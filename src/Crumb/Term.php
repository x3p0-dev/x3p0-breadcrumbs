<?php

/**
 * Term crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb;

use WP_Term;
use X3P0\Breadcrumbs\Contracts\Builder;

class Term extends Crumb
{
	/**
	 * {@inheritdoc}
	 */
	public function __construct(
		protected Builder $builder,
		protected WP_Term $term
	) {}

	/**
	 * {@inheritdoc}
	 */
	public function label(): string
	{
		$tax     = $this->term->taxonomy;
		$term_id = $this->term->term_id;

		if (
			is_tax($tax, $term_id)
			|| is_category($term_id)
			|| is_tag($term_id)
		) {
			return single_term_title('', false);
		}

		return $this->term->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function url(): string
	{
		return get_term_link($this->term, $this->term->taxonomy);
	}
}
