<?php

/**
 * Query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query;

use WP_Exception;
use X3P0\Breadcrumbs\BreadcrumbsContext;

/**
 * Abstract base for the query classes, each paired with a WordPress request
 * type (front page, singular, archive, etc.). A query's job is to translate
 * the current request into a sequence of breadcrumb-building steps, dispatched
 * through the `BreadcrumbsContext`: it may delegate to another query, run an
 * assembler, or append a single crumb. This is the contract that concrete
 * `Type\*` queries implement and that callers typehint against.
 */
abstract class Query
{
	/**
	 * The container tag under which query types are collected, so the
	 * full set — built-in and third-party — can be resolved by key and
	 * enumerated for the block editor. `QueryServiceProvider` seeds it
	 * from `QueryType`.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	public const TAG = 'x3p0/breadcrumbs/query';

	/**
	 * Stores the shared context object so subclasses can dispatch further
	 * queries, assemblers, and crumbs into the trail being built.
	 */
	public function __construct(protected readonly BreadcrumbsContext $context)
	{}

	/**
	 * Dispatches the steps that build the breadcrumb trail for this request
	 * type, appending to the context's shared crumb collection.
	 */
	abstract public function query(): void;

	/**
	 * Returns the queried object when it is an instance of the given type,
	 * or null otherwise. Queries read the queried object from the global
	 * WordPress query, which other code can leave in an unexpected state
	 * (e.g., a plugin modifying the main query on an archive so it also
	 * looks singular), so callers use this to validate the object before
	 * building the crumbs that depend on it rather than passing a wrong type
	 * into a typed assembler. When the object is present but the wrong type,
	 * it reports the mismatch via `wp_trigger_error()` (which surfaces it
	 * only under `WP_DEBUG`) while still degrading silently on the front end.
	 *
	 * @param  class-string $type
	 * @throws WP_Exception
	 */
	protected function queriedObject(string $type): ?object
	{
		$object = get_queried_object();

		if ($object instanceof $type) {
			return $object;
		}

		// A non-null object of the wrong type means the main query was
		// left in an inconsistent state, most likely by other code that
		// ran earlier (e.g., on `pre_get_posts`). Report the mismatch
		// so developers can find it — `wp_trigger_error()` only surfaces
		// it under `WP_DEBUG` — but return null so the trail degrades
		// safely. No backtrace is included: the manipulation happened
		// on an earlier hook.
		if (null !== $object) {
			wp_trigger_error(__METHOD__, sprintf(
				// Translators: 1 is the expected class name for the object, and 2 is the given class.
				__('Expected the queried object to be an instance of %1$s but received %2$s, which usually means the main query was modified by other code.', 'x3p0-breadcrumbs'),
				'<code>' . $type . '</code>',
				'<code>' . $object::class . '</code>'
			));
		}

		return null;
	}
}
