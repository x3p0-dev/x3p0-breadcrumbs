<?php

/**
 * Front page query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Query;

use X3P0\Breadcrumbs\Tools\Helpers;

class FrontPage extends Query
{
	/**
	 * {@inheritdoc}
	 */
	public function make(): void
	{
		if (
			$this->breadcrumbs->option('show_on_front')
			|| Helpers::isPagedView()
		) {
			$this->breadcrumbs->build('home');
			$this->breadcrumbs->build('paged');
		}
	}
}
