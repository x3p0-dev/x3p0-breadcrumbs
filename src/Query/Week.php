<?php

/**
 * Week query class.
 *
 * Called to build breadcrumbs on weekly archive pages.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Query;

class Week extends Base
{
	/**
	 * Builds the breadcrumbs.
	 *
	 * @since 1.0.0
	 */
	public function make(): void
	{
		// Build network crumbs.
		$this->breadcrumbs->build('Network');

		// Add site home crumb.
		$this->breadcrumbs->crumb('Home');

		// Build rewrite front crumbs.
		$this->breadcrumbs->build('RewriteFront');

		// Add the year and week crumbs.
		$this->breadcrumbs->crumb('Year');
		$this->breadcrumbs->crumb('Week');

		// Build paged crumbs.
		$this->breadcrumbs->build('Paged');
	}
}
