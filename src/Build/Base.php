<?php
/**
 * Build class.
 *
 * This is the base class, which should be sub-classed, for creating a specific
 * breadcrumb build tools. Build classes are essentially tools/methods for
 * handling specific scenarios. Quite often, they are the workhorses of the
 * project and create many of the crumbs.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Build;

use X3P0\Breadcrumbs\Contracts\Breadcrumbs;
use X3P0\Breadcrumbs\Contracts\Build;

abstract class Base implements Build
{
	/**
	 * Breadcrumbs object.
	 *
	 * @since 1.0.0
	 */
	protected Breadcrumbs $breadcrumbs;

	/**
	 * Creates a new build object. Any data passed in within the `$data`
	 * array will be automatically assigned to any existing properties, which
	 * can be useful for sub-classes that have custom properties.
	 *
	 * @since 1.0.0
	 */
	public function __construct( Breadcrumbs $breadcrumbs, array $data = [] )
	{
		foreach ( array_keys( get_object_vars( $this ) ) as $key ) {
			if ( isset( $data[ $key ] ) ) {
				$this->$key = $data[ $key ];
			}
		}

		$this->breadcrumbs = $breadcrumbs;
	}

	/**
	 * This should be overwritten in a sub-class. It's where the work happens
	 * to build out breadcrumbs.
	 *
	 * @since 1.0.0
	 */
	abstract public function make(): void;
}
