<?php

/**
 * Search crumb class.
 *
 * Creates the search results crumb.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Crumb;

class Search extends Base
{
	public function label(): string
	{
		return sprintf($this->breadcrumbs->label('search'), get_search_query());
	}

	public function url(): string
	{
		return get_search_link();
	}
}
