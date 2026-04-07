<?php

/**
 * Query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query;

use X3P0\Breadcrumbs\BreadcrumbsContext;

/**
 * `Query` classes are meant to be paired with the global WordPress queried URL,
 * such as the front page, single posts, archives, etc. Their purpose is to
 * call either `Assembler` or `Crumb` classes to generate breadcrumbs.
 */
abstract class Query
{
	/**
	 * Creates a new query object.
	 */
	public function __construct(protected BreadcrumbsContext $context)
	{}

	/**
	 * Runs the logic for generating breadcrumbs.
	 */
	abstract public function query(): void;
}
