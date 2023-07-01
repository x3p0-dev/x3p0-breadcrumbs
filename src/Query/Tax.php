<?php
/**
 * Taxonomy query class.
 *
 * Called to build breadcrumbs on taxonomy (term) archive pages.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace X3P0\Breadcrumbs\Query;

/**
 * Taxonomy query sub-class.
 *
 * @since  1.0.0
 * @access public
 */
class Tax extends Base {

	/**
	 * Term object.
	 *
	 * @since  1.2.0
	 * @access protected
	 * @var    \WP_Term
	 */
	protected $term;

	/**
	 * Builds the breadcrumbs.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function make() {

		$term = $this->term ?: get_queried_object();

		// Build network crumbs.
		$this->breadcrumbs->build( 'Network' );

		// Add site home crumb.
		$this->breadcrumbs->crumb( 'Home' );

		// Build term crumbs.
		$this->breadcrumbs->build( 'Term', [ 'term' => $term ] );

		// Add term crumb.
		$this->breadcrumbs->crumb( 'Term', [ 'term' => $term ] );

		// Build paged crumbs.
		$this->breadcrumbs->build( 'Paged' );
	}
}
