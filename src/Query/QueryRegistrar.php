<?php

/**
 * Query registrar class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query;

/**
 * Registers query classes with the registry.
 */
final class QueryRegistrar
{
	public const ARCHIVE           = 'archive';
	public const AUTHOR            = 'author';
	public const DATE              = 'date';
	public const ERROR_404         = 'error-404';
	public const FRONT_PAGE        = 'front-page';
	public const HOME              = 'home';
	public const PAGED             = 'paged';
	public const POST_TYPE_ARCHIVE = 'post-type-archive';
	public const SEARCH            = 'search';
	public const SINGULAR          = 'singular';
	public const TAXONOMY          = 'taxonomy';

	/**
	 * An array of query keys and their associated classes, to be stored
	 * in the query registry.
	 */
	private static function getQueries(): array
	{
		return [
			self::ARCHIVE           => Type\Archive::class,
			self::AUTHOR            => Type\Author::class,
			self::DATE              => Type\Date::class,
			self::ERROR_404         => Type\Error::class,
			self::FRONT_PAGE        => Type\FrontPage::class,
			self::HOME              => Type\Home::class,
			self::PAGED             => Type\Paged::class,
			self::POST_TYPE_ARCHIVE => Type\PostTypeArchive::class,
			self::SEARCH            => Type\Search::class,
			self::SINGULAR          => Type\Singular::class,
			self::TAXONOMY          => Type\Tax::class,
		];
	}

	/**
	 * Registers default queries with the registry.
	 */
	public static function register(QueryRegistry $queryRegistry): void
	{
		foreach (self::getQueries() as $key => $className) {
			if (! $queryRegistry->isRegistered($key)) {
				$queryRegistry->register($key, $className);
			}
		}
	}
}
