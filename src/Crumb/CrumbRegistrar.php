<?php

/**
 * Crumb registrar class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb;

/**
 * Registers crumb classes with the registry.
 */
final class CrumbRegistrar
{
	public const ARCHIVE           = 'archive';
	public const AUTHOR            = 'author';
	public const DAY               = 'day';
	public const ERROR_404         = 'error-404';
	public const HOME              = 'home';
	public const HOUR              = 'hour';
	public const MINUTE            = 'minute';
	public const MONTH             = 'month';
	public const NETWORK           = 'network';
	public const NETWORK_SITE      = 'network-site';
	public const PAGED             = 'paged';
	public const PAGED_COMMENTS    = 'paged-comments';
	public const PAGED_QUERY_BLOCK = 'paged-query-block';
	public const PAGED_SINGULAR    = 'paged-singular';
	public const POST              = 'post';
	public const POST_TYPE         = 'post-type';
	public const SEARCH            = 'search';
	public const SECOND            = 'second';
	public const TERM              = 'term';
	public const WEEK              = 'week';
	public const YEAR              = 'year';

	/**
	 * An array of crumb keys and their associated classes, to be stored
	 * in the crumb registry.
	 */
	private static function getCrumbs(): array
	{
		return [
			self::ARCHIVE           => Type\Archive::class,
			self::AUTHOR            => Type\Author::class,
			self::DAY               => Type\Day::class,
			self::ERROR_404         => Type\Error404::class,
			self::HOME              => Type\Home::class,
			self::HOUR              => Type\Hour::class,
			self::MINUTE            => Type\Minute::class,
			self::MONTH             => Type\Month::class,
			self::NETWORK           => Type\Network::class,
			self::NETWORK_SITE      => Type\NetworkSite::class,
			self::PAGED             => Type\Paged::class,
			self::PAGED_COMMENTS    => Type\PagedComments::class,
			self::PAGED_QUERY_BLOCK => Type\PagedQueryBlock::class,
			self::PAGED_SINGULAR    => Type\PagedSingular::class,
			self::POST              => Type\Post::class,
			self::POST_TYPE         => Type\PostType::class,
			self::SEARCH            => Type\Search::class,
			self::SECOND            => Type\Second::class,
			self::TERM              => Type\Term::class,
			self::WEEK              => Type\Week::class,
			self::YEAR              => Type\Year::class,
		];
	}

	/**
	 * Registers default crumbs with the registry.
	 */
	public static function register(CrumbRegistry $crumbRegistry): void
	{
		foreach (self::getCrumbs() as $key => $className) {
			if (! $crumbRegistry->isRegistered($key)) {
				$crumbRegistry->register($key, $className);
			}
		}
	}
}
