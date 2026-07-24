<?php

/**
 * Crumb type enum.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb;

/**
 * The canonical built-in crumb types — the source of truth mapping each case
 * to its class via `className()`. `CrumbServiceProvider` tags each class under
 * `Crumb::TAG`, so a caller may pass the case or the class name to the crumb
 * methods.
 */
enum CrumbType implements CrumbDefinition
{
	case Archive;
	case Author;
	case Custom;
	case Day;
	case Error404;
	case Home;
	case Hour;
	case Minute;
	case Month;
	case Network;
	case NetworkSite;
	case Paged;
	case PagedComments;
	case PagedQueryBlock;
	case PagedSingular;
	case Post;
	case PostType;
	case Search;
	case Second;
	case Term;
	case User;
	case Week;
	case Year;

	/**
	 * @inheritDoc
	 */
	public function className(): string
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return match ($this) {
			self::Archive         => Type\Archive::class,
			self::Author          => Type\Author::class,
			self::Custom          => Type\Custom::class,
			self::Day             => Type\Day::class,
			self::Error404        => Type\Error404::class,
			self::Home            => Type\Home::class,
			self::Hour            => Type\Hour::class,
			self::Minute          => Type\Minute::class,
			self::Month           => Type\Month::class,
			self::Network         => Type\Network::class,
			self::NetworkSite     => Type\NetworkSite::class,
			self::Paged           => Type\Paged::class,
			self::PagedComments   => Type\PagedComments::class,
			self::PagedQueryBlock => Type\PagedQueryBlock::class,
			self::PagedSingular   => Type\PagedSingular::class,
			self::Post            => Type\Post::class,
			self::PostType        => Type\PostType::class,
			self::Search          => Type\Search::class,
			self::Second          => Type\Second::class,
			self::Term            => Type\Term::class,
			self::User            => Type\User::class,
			self::Week            => Type\Week::class,
			self::Year            => Type\Year::class
		};
	}
}
