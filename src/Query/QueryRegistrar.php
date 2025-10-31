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

final class QueryRegistrar
{
	/**
	 * An array of query keys and their associated classes, to be stored
	 * in the query registry.
	 */
	private const QUERIES = [
		'archive'           => Type\Archive::class,
		'author'            => Type\Author::class,
		'day'               => Type\Day::class,
		'error-404'         => Type\Error::class,
		'front-page'        => Type\FrontPage::class,
		'home'              => Type\Home::class,
		'hour'              => Type\Hour::class,
		'minute'            => Type\Minute::class,
		'minute-hour'       => Type\MinuteHour::class,
		'month'             => Type\Month::class,
		'paged'             => Type\Paged::class,
		'post-type-archive' => Type\PostTypeArchive::class,
		'search'            => Type\Search::class,
		'singular'          => Type\Singular::class,
		'taxonomy'          => Type\Tax::class,
		'week'              => Type\Week::class,
		'year'              => Type\Year::class
	];

	/**
	 * Registers default queries with the registry.
	 */
	public static function register(QueryRegistry $queryRegistry): void
	{
		foreach (self::QUERIES as $key => $queryClass) {
			if (! $queryRegistry->isRegistered($key)) {
				$queryRegistry->register($key, $queryClass);
			}
		}
	}
}
