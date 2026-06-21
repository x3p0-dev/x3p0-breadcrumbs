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
 * Enumerates the canonical query keys.
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
	 * Returns the query class associated with the type.
	 *
	 * @return class-string<Query>
	 */
	public function className(): string
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return match ($this) {
			self::Archive         => Type\Archive::class,
			self::Author          => Type\Author::class,
			self::Date            => Type\Date::class,
			self::Error404        => Type\Error404::class,
			self::FrontPage       => Type\FrontPage::class,
			self::Home            => Type\Home::class,
			self::Paged           => Type\Paged::class,
			self::PostTypeArchive => Type\PostTypeArchive::class,
			self::Search          => Type\Search::class,
			self::Singular        => Type\Singular::class,
			self::Taxonomy        => Type\Taxonomy::class
		};
	}
}
