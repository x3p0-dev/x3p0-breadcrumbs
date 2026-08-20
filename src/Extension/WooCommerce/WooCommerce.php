<?php

/**
 * WooCommerce extension.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Extension\WooCommerce;

use X3P0\Breadcrumbs\Crumb\Crumb;
use X3P0\Breadcrumbs\Crumb\Event\CrumbsBuilt;
use X3P0\Breadcrumbs\Crumb\Type\PagedArchive;
use X3P0\Breadcrumbs\Crumb\Type\Post as PostCrumb;
use X3P0\Breadcrumbs\Crumb\Type\PostType as PostTypeCrumb;
use X3P0\Breadcrumbs\Extension\Extension;
use X3P0\Breadcrumbs\Extension\WooCommerce\Crumb\CatalogOrder as CatalogOrderCrumb;
use X3P0\Breadcrumbs\Extension\WooCommerce\Crumb\Shop as ShopCrumb;
use X3P0\Breadcrumbs\Extension\WooCommerce\Crumb\StorePage as StorePageCrumb;
use X3P0\Breadcrumbs\Extension\WooCommerce\Support\CatalogOrder as CatalogOrderSlug;
use X3P0\Breadcrumbs\Extension\WooCommerce\Support\Endpoint as EndpointSlug;
use X3P0\Breadcrumbs\Icon\Event\IconOptionsRegistered;
use X3P0\Breadcrumbs\Icon\Icon;
use X3P0\Breadcrumbs\Icon\IconOption;
use X3P0\Breadcrumbs\Packages\Event\Listener\Listenable;
use X3P0\Breadcrumbs\Query\Event\QueryTypeResolving;

/**
 * Built-in WooCommerce integration. The base queries already build correct
 * trails for the shop, single products, and product taxonomies, since a product
 * is a public post type with an archive and the product taxonomies are ordinary
 * taxonomies — so the extension only relabels the product post type archive
 * crumb to read as the shop and adds the crumb for the sorting a product
 * listing is under, which core has no equivalent of. It does both on the
 * `CrumbsBuilt` event rather than by replacing the built-in crumb class, so
 * other extensions can relabel their own crumbs on the same event without one
 * overriding the others.
 *
 * The rest is the part core has no concept of: the cart, checkout, and My
 * Account pages, whose endpoints (orders, view-order, order-received, and the
 * rest) are query vars on the host page. Each gets a custom query, rerouted to
 * while resolving the query type, that roots the trail at the shop and adds a
 * leaf crumb for the active endpoint.
 */
final class WooCommerce extends Extension
{
	/**
	 * The store pages that get a crumb of their own, as `wc_get_page_id()`
	 * accepts them. Each corresponds to a `woocommerce-{page}` icon option
	 * registered below and to the slug {@see StorePageCrumb} builds from the
	 * same key.
	 *
	 * @var  array<int, string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const STORE_PAGES = ['cart', 'checkout', 'myaccount', 'terms'];

	/**
	 * @inheritDoc
	 */
	public function subscribeTo(Listenable $registry): void
	{
		$registry->listenTo($this->onIconOptionsRegistered(...));
		$registry->listenTo($this->onQueryTypeResolving(...));
		$registry->listenTo($this->onCrumbsBuilt(...));
	}

	/**
	 * Registers the default icons for WooCommerce's own icon option keys. The
	 * product post type and its taxonomies are already registered by the time
	 * this runs — they are an ordinary public post type and taxonomies — so
	 * they are retargeted with `setIcon()`, which swaps the icon and leaves
	 * the label the registrar derived from each object in place. The
	 * extension's own crumb types have no such counterpart and are registered
	 * outright, unlabeled: they carry a default icon only and are not offered
	 * as block controls.
	 *
	 * Every key here is a crumb slug from this extension, which is what those
	 * crumbs resolve their icons through — the defaults belong in the registry
	 * rather than in a crumb's own `getIcon()`, so a site owner's configured
	 * icon still outranks them and another extension can retarget them.
	 */
	public function onIconOptionsRegistered(IconOptionsRegistered $event): void
	{
		$event->options->setIcon(IconOption::postTypeKey('product'),       Icon::Package);
		$event->options->setIcon(IconOption::taxonomyKey('product_brand'), Icon::BrandingWatermark);
		$event->options->setIcon(IconOption::taxonomyKey('product_cat'),   'core/category');
		$event->options->setIcon(IconOption::taxonomyKey('product_tag'),   'core/tag');
		$event->options->setIcon(IconOption::taxonomyKey('pa_color'),      Icon::Color);
		$event->options->setIcon(IconOption::taxonomyKey('pa_size'),       Icon::Straighten);

		$event->options->add(
			new IconOption('woocommerce-shop',             'core/store'),
			new IconOption('woocommerce-endpoint',         'core/more-vertical'),
			new IconOption('woocommerce-shipping-address', Icon::Shipping),
			new IconOption('woocommerce-billing-address',  Icon::ReceiptLong),
			new IconOption('woocommerce-cart',             'core/cart'),
			new IconOption('woocommerce-checkout',         'core/payment'),
			new IconOption('woocommerce-myaccount',        'core/people'),
			new IconOption('woocommerce-terms',            Icon::List),
			new IconOption('woocommerce-orderby',          'core/chevron-up-down')
		);

		$this->addEndpointIconOptions($event);
		$this->addCatalogOrderIconOptions($event);
	}

	/**
	 * Registers an option per endpoint the plugin names, keyed under the
	 * shared `woocommerce-endpoint` option so each carries its own default
	 * without the crumb hardcoding one. Endpoints WooCommerce or a third party
	 * adds that aren't named in {@see EndpointSlug} have no key of their own
	 * and resolve the shared option instead.
	 */
	private function addEndpointIconOptions(IconOptionsRegistered $event): void
	{
		foreach (EndpointSlug::cases() as $endpoint) {
			$event->options->add(
				new IconOption($endpoint->optionKey(), $endpoint->icon())
			);
		}
	}

	/**
	 * Registers an option per sorting option the plugin names, keyed under
	 * the shared `woocommerce-orderby` option so each carries its own default
	 * without the crumb hardcoding one. Sorting options a third party adds
	 * have no key of their own and resolve the shared option instead.
	 */
	private function addCatalogOrderIconOptions(IconOptionsRegistered $event): void
	{
		foreach (CatalogOrderSlug::cases() as $order) {
			$event->options->add(
				new IconOption($order->optionKey(), $order->icon())
			);
		}
	}

	/**
	 * Reroutes the endpoint-bearing store pages to their custom query
	 * before the built-in singular query would claim them, then stops
	 * propagation to keep the final say. Everything else — including the
	 * shop, products, and product taxonomies, which the base queries handle
	 * — falls through untouched.
	 */
	public function onQueryTypeResolving(QueryTypeResolving $event): void
	{
		$type = match (true) {
			is_account_page() => Query\Account::class,
			is_cart()         => Query\Cart::class,
			is_checkout()     => Query\Checkout::class,
			default           => null
		};

		if ($type) {
			$event->queryType = $type;
			$event->stopPropagation();
		}
	}

	/**
	 * When the shop page is the site's front page, first removes the
	 * product post type archive crumb entirely, since the home crumb
	 * already represents it. Otherwise, replaces that crumb with the shop
	 * crumb wherever it appears, so the archive reads as the shop without
	 * overriding the built-in post type crumb class. Then does the same for
	 * the store pages, which — unlike a product or its taxonomy terms — are
	 * ordinary `page`-type posts that nothing else marks out as a distinct
	 * kind of place. Endpoint crumbs need no such step; `Endpoint` is
	 * WooCommerce's own class already and names its own icon option, the same
	 * way it already resolves its own label.
	 */
	public function onCrumbsBuilt(CrumbsBuilt $event): void
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
		$this->addCatalogOrderCrumb($event);
	}

	/**
	 * Adds the crumb for the sorting a product listing is under — the shop,
	 * a product taxonomy archive, or a product search, all of which
	 * WooCommerce sorts by the same `orderby` request var. Without it, a
	 * sorted listing reads exactly as the unsorted one does.
	 *
	 * The crumb goes in ahead of the pagination crumb, since a page number
	 * is a position within the sorted listing rather than a step of its own,
	 * and is appended when the listing is unpaged.
	 */
	private function addCatalogOrderCrumb(CrumbsBuilt $event): void
	{
		if (! is_shop() && ! is_product_taxonomy()) {
			return;
		}

		$orderby = CatalogOrderSlug::requested();

		// The default sorting is what the listing already shows without
		// an `orderby` var at all, so it is not a step in the trail.
		if ('' === $orderby || CatalogOrderSlug::MenuOrder->value === $orderby) {
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

		if ($paged) {
			$event->crumbs->insertBefore($paged, $crumb);
		} else {
			$event->crumbs->push($crumb);
		}
	}

	/**
	 * Replaces the Cart, Checkout, and My Account page crumbs with the store
	 * page crumb — matched by post ID, since each is just an ordinary
	 * `page`-type post the site owner configured under WooCommerce's
	 * settings, and nothing about the post itself says which one it is.
	 */
	private function replaceStorePages(CrumbsBuilt $event): void
	{
		foreach (self::STORE_PAGES as $page) {
			$pageId = absint(wc_get_page_id($page));

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
		return 'posts' !== get_option('show_on_front')
			&& absint(wc_get_page_id('shop')) === absint(get_option('page_on_front'));
	}
}
