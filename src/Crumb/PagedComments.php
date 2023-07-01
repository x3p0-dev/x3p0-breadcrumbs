<?php
/**
 * Paged comments crumb class.
 *
 * Creates the paged comments crumb.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace X3P0\Breadcrumbs\Crumb;

/**
 * Paged comments crumb sub-class.
 *
 * @since  1.0.0
 * @access public
 */
class PagedComments extends Base {

	/**
	 * Returns a label for the crumb.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function label() {

		return sprintf(
			$this->breadcrumbs->label( 'paged_comments' ),
			number_format_i18n( absint( get_query_var( 'cpage' ) ) )
		);
	}
}
