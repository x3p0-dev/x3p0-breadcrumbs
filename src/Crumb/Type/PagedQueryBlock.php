<?php

/**
 * Crumb representing the current page of a paginated Query Loop block. Its
 * label is the configured "paged" string filled with the block's page number,
 * and its URL points back to the current request.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Type;

use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Crumb\Crumb;
use X3P0\Breadcrumbs\Support\Pagination;

final class PagedQueryBlock extends Crumb
{
	/**
	 * @inheritDoc
	 */
	public function __construct(
		BreadcrumbsContext $context,
		private readonly Pagination $pagination
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function getLabel(): string
	{
		return sprintf(
			$this->context->config()->getLabel('paged'),
			number_format_i18n(absint($this->pagination->getQueryBlockPage()))
		);
	}

	/**
	 * @inheritDoc
	 */
	public function getUrl(): string
	{
		return add_query_arg([]);
	}
}
