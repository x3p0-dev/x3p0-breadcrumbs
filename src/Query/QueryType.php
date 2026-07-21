<?php

/**
 * Query type enum.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query;

use X3P0\Breadcrumbs\Crumb\Crumb;

/**
 * The canonical built-in query types, one per WordPress request type — the
 * source of truth mapping each key to its class via `className()`.
 */
enum QueryType: string implements QueryDefinition
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
	 * @inheritDoc
	 * @return class-string<Crumb>
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
