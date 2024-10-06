<?php

/**
 * Minute query class.
 *
 * Called to build breadcrumbs on minute archive pages.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Query;

class Minute extends Base
{
	public function make(): void
	{
		$this->breadcrumbs->build('home');
		$this->breadcrumbs->build('rewrite-front');
		$this->breadcrumbs->crumb('minute');
		$this->breadcrumbs->build('paged');
	}
}
