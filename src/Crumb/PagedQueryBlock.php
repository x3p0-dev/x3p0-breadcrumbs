<?php

/**
 * Paged Query Block crumb class.
 *
 * Creates the paged crumb when a Query Loop block doesn't inherit from the
 * current query but is paged.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Crumb;

use X3P0\Breadcrumbs\Util\Helpers;

class PagedQueryBlock extends Base
{
	public function label(): string
	{
		return sprintf(
			$this->breadcrumbs->label('paged'),
			number_format_i18n(absint(Helpers::getQueryBlockPage()))
		);
	}
}
