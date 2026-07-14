<?php

/**
 * Crumbs built event.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Event;

use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Crumb\CrumbCollection;

/**
 * Dispatched after the query has populated the crumbs, before they are returned.
 * Listeners receive the finished collection along with the build context, so
 * they can append, remove, or relabel crumbs regardless of which query produced
 * them. The collection is the same instance the build used and is mutable, so
 * changes made here are what callers ultimately receive; use the context's
 * `addCrumb()` to append a crumb built through its factory.
 */
final class CrumbsBuilt
{
	/**
	 * The name of the WordPress action this event is bridged to after it
	 * is dispatched, so `add_action()` callbacks can adjust the finished
	 * crumbs alongside the typed listeners.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	public const HOOK_NAME = 'x3p0/breadcrumbs/crumbs-built';

	/**
	 * Stores the shared build context and the finished, mutable crumb
	 * collection (the same instance returned by `$context->crumbs()`).
	 */
	public function __construct(
		public readonly BreadcrumbsContext $context,
		public readonly CrumbCollection $crumbs
	) {}
}
