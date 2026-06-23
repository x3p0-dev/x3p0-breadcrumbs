<?php

/**
 * Query type enum.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query;

/**
 * The canonical string keys for the built-in query types, one per WordPress
 * request type. The registrar uses these to seed the registry, and the values
 * double as the keys callers pass to `BreadcrumbsContext::query()`.
 */
enum QueryType: string
{
	case Archive         = 'archive';
	case Author          = 'author';
	case Date            = 'date';
	case Error404        = 'error-404';
	case FrontPage       = 'front-page';
	case Home            = 'home';
	case Paged           = 'paged';
	case PostTypeArchive = 'post-type-archive';
	case Search          = 'search';
	case Singular        = 'singular';
	case Taxonomy        = 'taxonomy';

	/**
	 * Returns the query class associated with the type. Each case name matches
	 * a concrete class under the `Type` sub-namespace.
	 *
	 * @return class-string<Query>
	 */
	public function className(): string
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return __NAMESPACE__ . '\\Type\\' . $this->name;
	}
}
