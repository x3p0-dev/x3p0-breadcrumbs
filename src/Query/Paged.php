<?php

/**
 * Paged query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query;

class Paged extends Query
{
	/**
	 * {@inheritdoc}
	 */
	public function make(): void
	{
		$this->breadcrumbs->assemble('home');
		$this->breadcrumbs->assemble('rewrite-front');
		$this->breadcrumbs->assemble('paged');
	}
}
