<?php

/**
 * Paged archive crumb base class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Type;

use X3P0\Breadcrumbs\BreadcrumbsLabel;
use X3P0\Breadcrumbs\Crumb\Crumb;

/**
 * Base for the crumbs that mark the current page of something paginated
 * (`Paged`, `PagedComments`, `PagedQueryBlock`, and `PagedSingular`). Each
 * concrete type resolves its own page number and URL, since WordPress splits
 * pagination across several query vars and link functions, but they all share
 * a "Page %s"-style label and point at the same `paged` icon option, so all of
 * them are configured with one setting.
 */
abstract class PagedArchive extends Crumb
{
	/**
	 * Returns the page number the crumb labels itself with. Falls back to
	 * the first page when the view's page query var is unset, so the label
	 * never reads as page zero.
	 */
	abstract protected function pageNumber(): int;

	/**
	 * Returns the label case the page number is filled into. Every paged
	 * crumb but `PagedComments` uses the generic "Page %s" copy.
	 */
	protected function labelKey(): BreadcrumbsLabel
	{
		return BreadcrumbsLabel::Paged;
	}

	/**
	 * @inheritDoc
	 */
	public function getLabel(): string
	{
		return sprintf(
			$this->config->getLabel($this->labelKey()),
			number_format_i18n($this->pageNumber())
		);
	}

	/**
	 * @inheritDoc
	 */
	public function iconOptionKey(): string
	{
		return 'paged';
	}
}
