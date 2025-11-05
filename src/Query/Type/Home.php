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

use X3P0\Breadcrumbs\Query\AbstractQuery;
use X3P0\Breadcrumbs\Query\QueryRegistrar;

final class Home extends AbstractQuery
{
	/**
	 * @inheritDoc
	 */
	public function query(): void
	{
		is_front_page()
			? $this->context->query(QueryRegistrar::FRONT_PAGE)
			: $this->context->query(QueryRegistrar::SINGULAR);
	}
}
