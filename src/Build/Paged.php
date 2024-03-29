<?php
/**
 * Paged build class.
 *
 * Builds out breadcrumbs based on whether we're currently viewing a "paged"
 * page. This handles archive-type pagination, single-post pagination via
 * `<!--nextpage-->`, and comments pagination.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Build;

use X3P0\Breadcrumbs\Util\Helpers;

class Paged extends Base
{
	/**
	 * Post object.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    \WP_Post
	 */
	protected $post;

	/**
	 * Builds the breadcrumbs.
	 *
	 * @since 1.0.0
	 */
	public function make(): void
	{
		// If viewing a paged archive-type page.
		if ( is_paged() ) {

			$this->breadcrumbs->crumb( 'Paged' );

		// If viewing a paged singular post.
		} elseif ( is_singular() && 1 < get_query_var( 'page' ) ) {

			$this->breadcrumbs->crumb( 'PagedSingular' );

		// If viewing a singular post with paged comments.
		} elseif ( is_singular() && get_option( 'page_comments' ) && 1 < get_query_var( 'cpage' ) ) {

			$this->breadcrumbs->crumb( 'PagedComments' );

		// If viewing a paged Query Loop block view.
		} elseif ( Helpers::isPagedQueryBlock() ) {

			$this->breadcrumbs->crumb( 'PagedQueryBlock' );
		}
	}
}
