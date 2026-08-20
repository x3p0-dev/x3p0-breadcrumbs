<?php

/**
 * Paged query block crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Type;

use X3P0\Breadcrumbs\Crumb\CrumbContext;
use X3P0\Breadcrumbs\Support\Pagination;

/**
 * Crumb representing the current page of a paginated Query Loop block. Its
 * label is the configured "paged" string filled with the block's page number,
 * and its URL points back to the current request.
 */
final class PagedQueryBlock extends PagedArchive
{
	/**
	 * @inheritDoc
	 */
	public function __construct(
		CrumbContext $context,
		private readonly Pagination $pagination
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function getSlug(): string
	{
		return 'paged-query-block';
	}

	/**
	 * @inheritDoc
	 */
	protected function pageNumber(): int
	{
		return absint($this->pagination->getQueryBlockPage()) ?: 1;
	}

	/**
	 * @inheritDoc
	 */
	public function getUrl(): string
	{
		return add_query_arg([]);
	}
}
