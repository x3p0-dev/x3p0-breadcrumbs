<?php
/**
 * Search crumb class.
 *
 * Creates the search results crumb.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace X3P0\Breadcrumbs\Crumb;

class Search extends Base
{
	/**
	 * Returns a label for the crumb.
	 *
	 * @since 1.0.0
	 */
	public function label(): string
	{
		return sprintf( $this->breadcrumbs->label( 'search' ), get_search_query() );
	}

	/**
	 * Returns a URL for the crumb.
	 *
	 * @since 1.0.0
	 */
	public function url(): string
	{
		return get_search_link();
	}
}
