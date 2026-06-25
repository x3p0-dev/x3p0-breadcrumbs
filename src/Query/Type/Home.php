<?php

/**
 * Home query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query\Type;

use X3P0\Breadcrumbs\Query\Query;
use X3P0\Breadcrumbs\Query\QueryType;

/**
 * Dispatcher for the blog posts index ("home"). Forwards to the front page
 * query when the posts index is the site's front page, otherwise to the
 * singular query (the posts index is a static page in that case).
 */
final class Home extends Query
{
	/**
	 * @inheritDoc
	 */
	public function query(): void
	{
		is_front_page()
			? $this->context->query(QueryType::FrontPage)
			: $this->context->query(QueryType::Singular);
	}
}
