<?php

/**
 * WooCommerce catalog order crumb.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Extension\WooCommerce\Crumb;

use X3P0\Breadcrumbs\Crumb\Crumb;
use X3P0\Breadcrumbs\Crumb\CrumbContext;
use X3P0\Breadcrumbs\Extension\WooCommerce\Support\CatalogOrder as CatalogOrderSlug;

/**
 * Crumb representing the sorting applied to a product listing — the shop, a
 * product taxonomy archive, or a product search — which WooCommerce serves as
 * an `orderby` request var on the archive and which would otherwise leave the
 * trail reading exactly as it does unsorted.
 *
 * The sorting option is kept as the raw request value rather than an enum case,
 * since a plugin may register options of its own; see {@see CatalogOrderSlug}
 * for how the label is resolved for any of them.
 */
final class CatalogOrder extends Crumb
{
	/**
	 * Stores the raw `orderby` request value naming the sorting option
	 * (e.g. `popularity` or `price-desc`).
	 */
	public function __construct(
		CrumbContext $context,
		public readonly string $orderby
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function getSlug(): string
	{
		return 'woocommerce-orderby';
	}

	/**
	 * @inheritDoc
	 */
	public function getLabel(): string
	{
		return CatalogOrderSlug::labels()[$this->orderby] ?? '';
	}

	/**
	 * @inheritDoc
	 */
	public function getUrl(): string
	{
		// The sorted listing is the current view, and there is no link
		// function that rebuilds it: the sorting is one request var
		// among however many others — a price range, a layered nav
		// filter — that the view is also narrowed by, and every one of
		// them has to survive. So mirror what WordPress's own
		// pagination links do and capture the real request URL, reset
		// to the first page, which is this crumb's view whatever page
		// of it is being read. The URL is escaped at the point of
		// output, so return it unescaped.
		return get_pagenum_link(1, false);
	}

	/**
	 * Resolves the option registered for this specific sorting option when it
	 * is one the plugin has an opinion about, so each carries its own default
	 * and can be configured on its own. Anything a third party registered
	 * falls back to the slug — the shared `woocommerce-orderby` option, which
	 * applies uniformly to all of them.
	 *
	 * @inheritDoc
	 */
	protected function iconOptionKey(): string
	{
		return CatalogOrderSlug::tryFrom($this->orderby)?->optionKey() ?? $this->getSlug();
	}
}
