<?php

/**
 * Paged singular crumb class.
 *
 * Creates the paged singular crumb when `<!--nextpage-->` is used for a post.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Crumb;

class PagedSingular extends Base
{
	/**
	 * Returns a label for the crumb.
	 *
	 * @since 1.0.0
	 */
	public function label(): string
	{
		return sprintf(
			$this->breadcrumbs->label('paged'),
			number_format_i18n(absint(get_query_var('page')))
		);
	}
}
