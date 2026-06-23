<?php

/**
 * Crumb type enum.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb;

/**
 * Enumerates the canonical crumb keys.
 */
enum CrumbType: string
{
	case Archive         = 'archive';
	case Author          = 'author';
	case Day             = 'day';
	case Error404        = 'error-404';
	case Home            = 'home';
	case Hour            = 'hour';
	case Minute          = 'minute';
	case Month           = 'month';
	case Network         = 'network';
	case NetworkSite     = 'network-site';
	case Paged           = 'paged';
	case PagedComments   = 'paged-comments';
	case PagedQueryBlock = 'paged-query-block';
	case PagedSingular   = 'paged-singular';
	case Post            = 'post';
	case PostType        = 'post-type';
	case Search          = 'search';
	case Second          = 'second';
	case Term            = 'term';
	case Week            = 'week';
	case Year            = 'year';

	/**
	 * Returns the crumb class associated with the type. Each case name matches
	 * a concrete class under the `Type` sub-namespace.
	 *
	 * @return class-string<Crumb>
	 */
	public function className(): string
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return __NAMESPACE__ . '\\Type\\' . $this->name;
	}
}
