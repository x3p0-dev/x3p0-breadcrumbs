<?php
/**
 * Crumb class.
 *
 * This is the base class, which should be sub-classed, for creating a specific
 * breadcrumb item. Each sub-class should, at minimum, have a label. Not all
 * will necessarily have a URL if they're only designed to be the final crumb in
 * the trail.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace X3P0\Breadcrumbs\Crumb;

use X3P0\Breadcrumbs\Contracts\Breadcrumbs;
use X3P0\Breadcrumbs\Contracts\Crumb;

/**
 * Base crumb class.
 *
 * @since  1.0.0
 * @access public
 */
abstract class Base implements Crumb {

	/**
	 * Breadcrumbs object.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    Breadcrumbs
	 */
	protected $breadcrumbs;

	/**
	 * Creates a new crumb object. Any data passed in within the `$data`
	 * array will be automatically assigned to any existing properties, which
	 * can be useful for sub-classes that have custom properties.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  Breadcrumbs $breadcrumbs
	 * @param  array       $data
	 * @return void
	 */
	public function __construct( Breadcrumbs $breadcrumbs, array $data = [] ) {

		foreach ( array_keys( get_object_vars( $this ) ) as $key ) {

			if ( isset( $data[ $key ] ) ) {
				$this->$key = $data[ $key ];
			}
		}

		$this->breadcrumbs = $breadcrumbs;
	}

	/**
	 * Returns the type for the crumb. By default, we just use the PHP class
	 * name to build the type.  If wanting something custom, this should be
	 * handled in a sub-class.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function type() {

		$class = explode( '\\', get_class( $this ) );
		$class = array_pop( $class );

		$pascal = preg_split( '/((?<=[a-z])(?=[A-Z])|(?=[A-Z][a-z]))/', $class, -1, PREG_SPLIT_NO_EMPTY );

		return strtolower( join( '-', $pascal ) );
	}

	/**
	 * Returns a label for the crumb.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function label() {
		return '';
	}

	/**
	 * Returns a URL for the crumb.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function url() {
		return '';
	}
}
