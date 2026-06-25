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
}
