<?php

/**
 * Term crumb class.
 *
 * Creates the term crumb.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Crumb;

use WP_Term;
use X3P0\Breadcrumbs\Contracts\Breadcrumbs;

class Term extends Base
{
	public function __construct(
		protected Breadcrumbs $breadcrumbs,
		protected WP_Term $term
	) {}

	public function label(): string
	{
		$tax     = $this->term->taxonomy;
		$term_id = $this->term->term_id;

		if (is_tax($tax, $term_id) || is_category($term_id) || is_tag($term_id)) {
			return single_term_title('', false);
		}

		return $this->term->name;
	}

	public function url(): string
	{
		return get_term_link($this->term, $this->term->taxonomy);
	}
}
