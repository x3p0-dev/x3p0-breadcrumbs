<?php
/**
 * Author query class.
 *
 * Called to build breadcrumbs on author archive pages.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace X3P0\Breadcrumbs\Query;

use WP_User;

class Author extends Base
{
	/**
	 * User object.
	 *
	 * @since  1.2.0
	 * @access protected
	 * @var    \WP_User
	 */
	protected $user;

	/**
	 * Builds the breadcrumbs.
	 *
	 * @since 1.0.0
	 */
	public function make(): void
	{
		global $wp_rewrite;

		$user = $this->user ?: new WP_User( get_query_var( 'author' ) );

		// Build network crumbs.
		$this->breadcrumbs->build( 'Network' );

		// Add site home crumb.
		$this->breadcrumbs->crumb( 'Home' );

		// Build rewrite front crumbs.
		$this->breadcrumbs->build( 'RewriteFront' );

		// If $author_base exists, check for parent pages.
		if ( ! empty( $wp_rewrite->author_base ) ) {

			$this->breadcrumbs->build( 'Path', [
				'page' => $wp_rewrite->author_base
			] );
		}

		// Add author crumb.
		$this->breadcrumbs->crumb( 'Author', [ 'user' => $user ] );
	}
}
