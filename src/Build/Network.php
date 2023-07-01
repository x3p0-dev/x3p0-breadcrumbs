<?php
/**
 * Network build class.
 *
 * This class builds out breadcrumbs to point to the main site in multisite.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace X3P0\Breadcrumbs\Build;

/**
 * Network build sub-class.
 *
 * @since  1.0.0
 * @access public
 */
class Network extends Base {

	/**
	 * Builds the breadcrumbs.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function make() {

		if ( is_multisite() && $this->breadcrumbs->option( 'network' ) && ! is_main_site() ) {

			$this->breadcrumbs->crumb( 'Network' );
		}
	}
}
