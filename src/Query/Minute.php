<?php

/**
 * Minute query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query;

class Minute extends Query
{
	/**
	 * {@inheritdoc}
	 */
	public function query(): void
	{
		$this->builder->assemble('home');
		$this->builder->assemble('rewrite-front');
		$this->builder->addCrumb('minute');
		$this->builder->assemble('paged');
	}
}
