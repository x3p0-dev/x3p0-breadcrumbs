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
	case Attachment;
	case Archive;
	case Author;
	case Category;
	case Date;
	case Day;
	case Error404;
	case FrontPage;
	case Home;
	case Month;
	case Page;
	case Paged;
	case PostTypeArchive;
	case Search;
	case Single;
	case Singular;
	case Tag;
	case Taxonomy;
	case Time;
	case Year;

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
			self::Error404        => Type\Error404::class,
			self::FrontPage       => Type\FrontPage::class,
			self::Home            => Type\Home::class,
			self::Paged           => Type\Paged::class,
			self::PostTypeArchive => Type\PostTypeArchive::class,
			self::Search          => Type\Search::class,

			// Aliases for the `Date` case.
			self::Day,
			self::Date,
			self::Month,
			self::Time,
			self::Year            => Type\Date::class,

			// Aliases for the `Singular` case.
			self::Attachment,
			self::Page,
			self::Single,
			self::Singular        => Type\Singular::class,

			// Aliases for the `Taxonomy` case.
			self::Category,
			self::Tag,
			self::Taxonomy        => Type\Taxonomy::class
		};
	}
}
