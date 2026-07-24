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

/**
 * The canonical built-in query types, one per WordPress request type — the
 * source of truth mapping each key to its class via `className()`.
 */
enum QueryType implements QueryDefinition
{
	case Archive;
	case Author;
	case Date;
	case Error404;
	case FrontPage;
	case Home;
	case Paged;
	case PostTypeArchive;
	case Search;
	case Singular;
	case Taxonomy;

	/**
	 * @inheritDoc
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
