<?php
/**
 * Home crumb class.
 *
 * Creates the home crumb.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Crumb;

class Home extends Base
{
	/**
	 * Returns a label for the crumb.
	 *
	 * @since 1.0.0
	 */
	public function label(): string
	{
		$network = $this->breadcrumbs->option( 'network' ) && ! is_main_site();

		return $network ? get_bloginfo( 'name' ) : $this->breadcrumbs->label( 'home' );
	}

	/**
	 * Returns a URL for the crumb.
	 *
	 * @since 1.0.0
	 */
	public function url(): string
	{
		return user_trailingslashit( home_url() );
	}

	/**
	 * Returns whether the crumb should be visually hidden on display.
	 *
	 * @since 1.0.0
	 */
	public function visuallyHidden(): bool
	{
		$network = $this->breadcrumbs->option( 'network' ) && ! is_main_site();

		return $network ? false : ! $this->breadcrumbs->option( 'show_home_label' );
	}
}
