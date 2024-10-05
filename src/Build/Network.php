<?php

/**
 * Network build class.
 *
 * This class builds out breadcrumbs to point to the main site in multisite.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Build;

class Network extends Base
{
	/**
	 * Builds the breadcrumbs.
	 *
	 * @since 1.0.0
	 */
	public function make(): void
	{
		if (is_multisite() && $this->breadcrumbs->option('network') && ! is_main_site()) {
			$this->breadcrumbs->crumb('Network');
		}
	}
}
