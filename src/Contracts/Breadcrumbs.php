<?php
/**
 * Breadcrumbs interface.
 *
 * Defines the interface that breadcrumbs classes must use.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace X3P0\Breadcrumbs\Contracts;

/**
 * Breadcrumbs interface.
 *
 * @since  1.0.0
 * @access public
 */
interface Breadcrumbs {

	/**
	 * Builds a new breadcrumbs object and returns it.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return Breadcrumbs
	 */
	public function make();

	/**
	 * Renders the breadcrumbs HTML output.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function display();

	/**
	 * Returns the breadcrumbs HTML output.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function render();

	/**
	 * Returns the breadcrumbs in an array.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function all();

	/**
	 * Creates a new `\X3P0\Breadcrumbs\Contracts\Query` object and runs
	 * its `make()` method.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $type
	 * @param  array   $data
	 * @return void
	 */
	public function query( $type, array $data = [] );

	/**
	 * Creates a new `\X3P0\Breadcrumbs\Contracts\Build` object and runs
	 * its `make()` method.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $type
	 * @param  array   $data
	 * @return void
	 */
	public function build( $type, array $data = [] );

	/**
	 * Creates a new `\X3P0\Breadcrumbs\Contracts\Crumb` object and adds
	 * it to the array of crumbs.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $type
	 * @param  array   $data
	 * @return void
	 */
	public function crumb( $type, array $data = [] );

	/**
	 * Returns a specific option or `false` if the option doesn't exist.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $name
	 * @return mixed
	 */
	public function option( $name );

	/**
	 * Returns a specific label or an empty string if it doesn't exist.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $name
	 * @return string
	 */
	public function label( $name );

	/**
	 * Returns a specific post taxonomy or an empty string if one isn't set.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $post_type
	 * @return string
	 */
	public function postTaxonomy( $post_type );
}
