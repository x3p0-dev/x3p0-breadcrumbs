<?php

/**
 * Paged Query Block crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Crumb;

use X3P0\Breadcrumbs\Tools\Helpers;

class PagedQueryBlock extends Crumb
{
	/**
	 * {@inheritdoc}
	 */
	public function label(): string
	{
		return sprintf(
			$this->breadcrumbs->label('paged'),
			number_format_i18n(absint(Helpers::getQueryBlockPage()))
		);
	}
}
