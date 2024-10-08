<?php

/**
 * Home query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Query;

class Home extends Base
{
	/**
	 * {@inheritdoc}
	 */
	public function make(): void
	{
		is_front_page()
			? $this->breadcrumbs->query('front-page')
			: $this->breadcrumbs->query('singular');
	}
}
