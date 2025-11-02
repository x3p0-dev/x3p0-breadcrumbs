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

final class CrumbRegistrar
{
	/**
	 * An array of crumb keys and their associated classes, to be stored
	 * in the crumb registry.
	 */
	private const CRUMBS = [
		'archive'           => Type\Archive::class,
		'author'            => Type\Author::class,
		'day'               => Type\Day::class,
		'error-404'         => Type\Error404::class,
		'home'              => Type\Home::class,
		'minute'            => Type\Minute::class,
		'minute-hour'       => Type\MinuteHour::class,
		'month'             => Type\Month::class,
		'network'           => Type\Network::class,
		'network-site'      => Type\NetworkSite::class,
		'paged'             => Type\Paged::class,
		'paged-comments'    => Type\PagedComments::class,
		'paged-query-block' => Type\PagedQueryBlock::class,
		'paged-singular'    => Type\PagedSingular::class,
		'post'              => Type\Post::class,
		'post-type'         => Type\PostType::class,
		'search'            => Type\Search::class,
		'term'              => Type\Term::class,
		'week'              => Type\Week::class,
		'year'              => Type\Year::class
	];

	/**
	 * Registers default crumbs with the registry.
	 */
	public static function register(CrumbRegistry $crumbRegistry): void
	{
		foreach (self::CRUMBS as $key => $crumbClass) {
			if (! $crumbRegistry->isRegistered($key)) {
				$crumbRegistry->register($key, $crumbClass);
			}
		}
	}
}
