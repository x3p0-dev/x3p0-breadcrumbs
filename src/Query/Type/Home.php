<?php

/**
 * Home query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query\Type;

use X3P0\Breadcrumbs\Query\Query;

class Home extends Query
{
	/**
	 * {@inheritdoc}
	 */
	public function query(): void
	{
		is_front_page()
			? $this->builder->query('front-page')
			: $this->builder->query('singular');
	}
}
