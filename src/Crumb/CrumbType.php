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
 * The canonical built-in crumb types — the source of truth mapping each key to
 * its class via `className()`. `CrumbServiceProvider` registers each value as a
 * container alias for that class, so a caller may pass the case, its string key,
 * or the class name to the crumb methods. Each case value is also the crumb's
 * type slug.
 */
enum CrumbType: string
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
	case User            = 'user';
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
			self::User            => Type\User::class,
			self::Week            => Type\Week::class,
			self::Year            => Type\Year::class
		};
	}

	/**
	 * Returns this case's container alias — its key namespaced under the
	 * subsystem as `x3p0/breadcrumbs/{TYPE}/{key}` — so the same short key can
	 * be reused across subsystems without colliding in the container's single,
	 * global alias table.
	 */
	public function alias(): string
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return 'x3p0/breadcrumbs/crumb/' . $this->value;
	}
}
