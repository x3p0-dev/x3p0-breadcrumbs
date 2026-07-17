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

use X3P0\Breadcrumbs\Support\ClassEnum;

/**
 * Canonical string keys for the built-in crumb types. Each case value is the
 * key used in the registry, and each case name matches a concrete class under
 * the `Type` sub-namespace (see `className()`), so the registrar can map every
 * case to its class automatically.
 */
enum CrumbType: string implements ClassEnum
{
	case Archive         = 'archive';
	case Author          = 'author';
	case Custom          = 'custom';
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
	 * Returns the crumb class associated with the type, mapping each case
	 * to a concrete class under the `Type` sub-namespace.
	 *
	 * @return class-string<Crumb>
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
			self::Week            => Type\Week::class,
			self::Year            => Type\Year::class
		};
	}
}
