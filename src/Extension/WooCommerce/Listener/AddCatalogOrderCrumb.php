<?php

/**
 * WooCommerce catalog order crumb listener.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Extension\WooCommerce\Listener;

use X3P0\Breadcrumbs\Crumb\Crumb;
use X3P0\Breadcrumbs\Crumb\Event\CrumbsBuilt;
use X3P0\Breadcrumbs\Crumb\Type\PagedArchive;
use X3P0\Breadcrumbs\Extension\WooCommerce\Crumb\CatalogOrder as CatalogOrderCrumb;
use X3P0\Breadcrumbs\Extension\WooCommerce\Support\CatalogOrder as CatalogOrderSlug;

/**
 * Adds the crumb for the sorting a product listing is under — the shop, a
 * product taxonomy archive, or a product search, all of which WooCommerce sorts
 * by the same `orderby` request var. Without it, a sorted listing reads exactly
 * as the unsorted one does.
 *
 * Unlike {@see RelabelStoreCrumbs}, which renames places the base queries
 * already found, this adds a step those queries have no concept of, so it is
 * its own listener on the same event: a site that wants its listings to read as
 * unsorted can drop this one alone.
 */
final class AddCatalogOrderCrumb
{
	/**
	 * Builds the sorting crumb and puts it ahead of the pagination crumb,
	 * since a page number is a position within the sorted listing rather than
	 * a step of its own, appending it when the listing is unpaged.
	 */
	public function __invoke(CrumbsBuilt $event): void
	{
		if (! is_shop() && ! is_product_taxonomy()) {
			return;
		}

		$orderby = CatalogOrderSlug::requested();

		// The default sorting is what the listing already shows without
		// an `orderby` var at all, so it is not a step in the trail.
		if ('' === $orderby || CatalogOrderSlug::MenuOrder->orderby() === $orderby) {
			return;
		}

		// A value the store offers no sorting option for — one a plugin
		// removed, or one that was never valid — has no label to show.
		if (! isset(CatalogOrderSlug::labels()[$orderby])) {
			return;
		}

		$crumb = $event->makeCrumb(CatalogOrderCrumb::class, [
			'orderby' => $orderby
		]);

		if (! $crumb) {
			return;
		}

		$paged = $event->crumbs->first(
			static fn (Crumb $item) => $item instanceof PagedArchive
		);

		$paged ? $event->crumbs->insertBefore($paged, $crumb) : $event->crumbs->push($crumb);
	}
}
