<?php

/**
 * Paged comments crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Type;

use X3P0\Breadcrumbs\Crumb\AbstractCrumb;

final class PagedComments extends AbstractCrumb
{
	/**
	 * {@inheritdoc}
	 */
	public function getLabel(): string
	{
		return sprintf(
			$this->builder->getLabel('paged_comments'),
			number_format_i18n(absint(get_query_var('cpage')))
		);
	}
}
