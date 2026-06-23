<?php

/**
 * Crumb representing a search results page. Its label is the configured
 * "search" string filled with the current query, and its URL is the search
 * results link.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Type;

use X3P0\Breadcrumbs\Crumb\Crumb;

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
