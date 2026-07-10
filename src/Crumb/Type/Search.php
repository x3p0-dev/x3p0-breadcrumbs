<?php

/**
 * Search crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Type;

use X3P0\Breadcrumbs\Crumb\Crumb;

/**
 * Crumb representing a search results page. Its label is the configured
 * "search" string filled with the current query, and its URL is the search
 * results link.
 */
final class Search extends Crumb
{
	/**
	 * @inheritDoc
	 */
	public function getLabel(): string
	{
		return sprintf($this->context->config()->getLabel('search'), get_search_query());
	}

	/**
	 * @inheritDoc
	 */
	public function getUrl(): string
	{
		return get_search_link();
	}
}
