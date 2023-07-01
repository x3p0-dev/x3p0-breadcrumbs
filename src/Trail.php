<?php
/**
 * Trail is a static helper class that works as an easy-to-use wrapper for the
 * `Breadcrumbs` class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

namespace X3P0\Breadcrumbs;

/**
 * Trail class.
 *
 * @since  1.0.0
 * @access public
 */
class Trail
{
	/**
	 * Returns a new breadcrumbs object.
	 *
	 * @since 1.0.0
	 */
	public static function breadcrumbs( array $args = [] ): Breadcrumbs
	{
		return new Breadcrumbs( $args );
	}

	/**
	 * Returns a new breadcrumbs object after calling its `make()` method.
	 *
	 * @since 1.0.0
	 */
	public static function make( array $args = [] ): Breadcrumbs
	{
		return static::breadcrumbs( $args )->make();
	}

	/**
	 * Returns an array of `\X3P0\Breadcrumbs\Contracts\Crumb` objects.
	 *
	 * @since 1.0.0
	 */
	public static function all( array $args = [] ): array
	{
		return static::make( $args )->all();
	}

	/**
	 * Renders the breadcrumb trail output.
	 *
	 * @since 1.0.0
	 */
	public static function display( array $args = [] ): void
	{
		static::make( $args )->display();
	}

	/**
	 * Returns the breadcrumb trail output.
	 *
	 * @since 1.0.0
	 */
	public static function render( array $args = [] ): string
	{
		return static::make( $args )->render();
	}
}
