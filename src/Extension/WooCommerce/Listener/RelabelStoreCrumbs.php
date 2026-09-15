<?php

/**
 * WooCommerce store crumb relabel listener.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Extension\WooCommerce\Listener;

use X3P0\Breadcrumbs\Crumb\Event\CrumbsBuilt;
use X3P0\Breadcrumbs\Crumb\Type\Post as PostCrumb;
use X3P0\Breadcrumbs\Crumb\Type\PostType as PostTypeCrumb;
use X3P0\Breadcrumbs\Extension\WooCommerce\Crumb\Shop as ShopCrumb;
use X3P0\Breadcrumbs\Extension\WooCommerce\Crumb\StorePage as StorePageCrumb;
use X3P0\Breadcrumbs\Extension\WooCommerce\Support\StorePage as StorePageSlug;

/**
 * Gives the store's own places an identity in a trail the base queries already
 * built correctly. Two kinds of crumb come out of those queries describing a
 * store place in WordPress's terms rather than the store's: the product post
 * type archive, which is the shop, and the store pages, which are ordinary
 * `page`-type posts that nothing marks out as a distinct kind of place.
 *
 * Both are handled by decorating the crumb the base query produced rather than
 * by replacing the built-in crumb class, so another extension can relabel its
 * own crumbs on this same event without one overriding the others. Endpoint
 * crumbs need no such step: `Crumb\Endpoint` is WooCommerce's own class
 * already and names its own icon option, the same way it resolves its own
 * label.
 */
final class RelabelStoreCrumbs
{
	/**
	 * When the shop page is the site's front page, removes the product post
	 * type archive crumb entirely, since the home crumb already represents
	 * it. Otherwise replaces that crumb with the shop crumb wherever it
	 * appears, so the archive reads as the shop.
	 */
	public function __invoke(CrumbsBuilt $event): void
	{
		if ($this->shopIsFrontPage()) {
			$event->crumbs->removeInstanceWhere(
				PostTypeCrumb::class,
				static fn (PostTypeCrumb $crumb) => 'product' === $crumb->postType->name
			);
		}

		$event->crumbs->replaceInstanceWhere(
			PostTypeCrumb::class,
			static fn (PostTypeCrumb $crumb) => 'product' === $crumb->postType->name,
			static fn (PostTypeCrumb $crumb) => $event->makeCrumb(
				ShopCrumb::class,
				['decoratedCrumb' => $crumb]
			)
		);

		$this->replaceStorePages($event);
	}

	/**
	 * Replaces the store page crumbs — the cart, checkout, My Account, and
	 * terms pages — with the store page crumb, matched by post ID, since each
	 * is just an ordinary `page`-type post the site owner configured under
	 * WooCommerce's settings and nothing about the post itself says which one
	 * it is.
	 */
	private function replaceStorePages(CrumbsBuilt $event): void
	{
		foreach (StorePageSlug::cases() as $page) {
			$pageId = $page->pageId();

			// A page the store has not configured, which
			// `wc_get_page_id()` reports as -1.
			if (0 >= $pageId) {
				continue;
			}

			$event->crumbs->replaceInstanceWhere(
				PostCrumb::class,
				static fn (PostCrumb $crumb) => $crumb->post->ID === $pageId,
				static fn (PostCrumb $crumb) => $event->makeCrumb(StorePageCrumb::class, [
					'decoratedCrumb' => $crumb,
					'page'           => $page
				])
			);
		}
	}

	/**
	 * Whether the shop page is configured as the site's static front page.
	 */
	private function shopIsFrontPage(): bool
	{
		$shopId = wc_get_page_id('shop');

		return 0 < $shopId
			&& 'posts' !== get_option('show_on_front')
			&& $shopId === absint(get_option('page_on_front'));
	}
}
