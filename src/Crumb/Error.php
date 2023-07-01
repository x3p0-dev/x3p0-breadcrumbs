<?php
/**
 * Error crumb class.
 *
 * Creates the error 404 crumb.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace X3P0\Breadcrumbs\Crumb;

/**
 * Error 404 crumb sub-class.
 *
 * @since  1.0.0
 * @access public
 */
class Error extends Base {

	/**
	 * Returns a label for the crumb.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function label() {

		return $this->breadcrumbs->label( 'error_404' );
	}
}
