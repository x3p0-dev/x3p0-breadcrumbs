<?php
/**
 * Front page query class.
 *
 * Called to build breadcrumbs on the front page.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace X3P0\Breadcrumbs\Query;

use X3P0\Breadcrumbs\Util\Helpers;

class FrontPage extends Base
{
	/**
	 * Builds the breadcrumbs.
	 *
	 * @since 1.0.0
	 */
	public function make(): void
	{
		if ( $this->breadcrumbs->option( 'show_on_front' ) || Helpers::isPagedView() ) {

			// Build network crumbs.
			$this->breadcrumbs->build( 'Network' );

			// Add site home crumb.
			$this->breadcrumbs->crumb( 'Home' );

			// Build paged crumbs.
			$this->breadcrumbs->build( 'Paged' );
		}
	}
}
