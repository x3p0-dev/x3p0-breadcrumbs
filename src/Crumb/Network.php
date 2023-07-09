<?php
/**
 * Network crumb class.
 *
 * Creates the network (link to main site in a multisite setup) crumb.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace X3P0\Breadcrumbs\Crumb;

class Network extends Base
{
	/**
	 * Returns a label for the crumb.
	 *
	 * @since 1.0.0
	 */
	public function label(): string
	{
		return $this->breadcrumbs->label( 'home' );
	}

	/**
	 * Returns a URL for the crumb.
	 *
	 * @since 1.0.0
	 */
	public function url(): string
	{
		return network_home_url();
	}

	/**
	 * Returns whether the crumb should be visually hidden on display.
	 *
	 * @since 1.0.0
	 */
	public function visuallyHidden(): bool
	{
		return ! $this->breadcrumbs->option( 'show_home_label' );
	}
}
