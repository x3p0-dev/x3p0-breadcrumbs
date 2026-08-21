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
use X3P0\Breadcrumbs\Icon\IconOptionKey;
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
	 * Key of the icon option group everything to do with the store is listed
	 * under in the block editor, keeping it together rather than scattered
	 * through the catch-all and the post type and taxonomy groups. It holds
	 * this extension's own options and the options for the WordPress objects
	 * the store owns alike: WordPress may model a product category the same
	 * way it models a blog category, but nobody running a store thinks of them
	 * as the same kind of thing.
	 */
	private const ICON_GROUP = 'woocommerce';

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
	 * Registers the icon options for the store, in the extension's own group so
	 * a site owner finds them together. Two kinds of thing end up there.
	 *
	 * The product post type and its taxonomies are already registered by the
	 * time this runs — they are an ordinary public post type and taxonomies —
	 * so they are amended with `update()`, which swaps the icon and moves them
	 * into the group while leaving the label and slug the registrar derived
	 * from each object in place. They belong under WooCommerce rather than
	 * among the generic post types and taxonomies: what a store owner thinks
	 * of as a category of their catalog isn't the same thing as a blog
	 * category, even though WordPress models both the same way.
	 *
	 * The extension's own crumb types have no such counterpart and are
	 * registered outright. Every key is a crumb slug from this extension,
	 * which is what those crumbs resolve their icons through — the defaults
	 * belong in the registry rather than in a crumb's own `getIcon()`, so a
	 * site owner's configured icon still outranks them and another extension
	 * can retarget them.
	 */
	public function onIconOptionsRegistered(IconOptionsRegistered $event): void
	{
		$event->options->addGroup(self::ICON_GROUP, __('WooCommerce', 'x3p0-breadcrumbs'));

		$event->options->update(IconOptionKey::postType('product'),       icon: Icon::Package);
		$event->options->update(IconOptionKey::taxonomy('product_brand'), icon: Icon::BrandingWatermark);
		$event->options->update(IconOptionKey::taxonomy('product_cat'),   icon: 'core/category');
		$event->options->update(IconOptionKey::taxonomy('product_tag'),   icon: 'core/tag');
		$event->options->update(IconOptionKey::taxonomy('pa_color'),      icon: Icon::Color);
		$event->options->update(IconOptionKey::taxonomy('pa_size'),       icon: Icon::Straighten);

		// The shop *is* the product post type archive — the `Shop` crumb
		// decorates that crumb wherever it appears — so the archive's option
		// is the shop's option, and there is no separate one for it. The name
		// follows the crumb's: with a shop page configured, both read as that
		// page's title; without one, the crumb falls back to the post type
		// archive's own label, so the option is left to do the same. Passing
		// null leaves it alone.
		$event->options->update(
			IconOptionKey::postTypeArchive('product'),
			icon: 'core/store',
			label: $this->shopPageTitle() ?: null
		);

		$this->groupProductObjectOptions($event);

		// The store pages are ordinary pages the site owner picked under
		// WooCommerce's settings, so their icons belong to the pages
		// themselves — set on each page, the way any other page's is — rather
		// than to a control here that names four of them out of every page on
		// the site. These carry their defaults and stay out of the editor.
		$event->options->add(
			new IconOption('woocommerce-cart',      'core/cart',    group: self::ICON_GROUP),
			new IconOption('woocommerce-checkout',  'core/payment', group: self::ICON_GROUP),
			new IconOption('woocommerce-myaccount', 'core/people',  group: self::ICON_GROUP),
			new IconOption('woocommerce-terms',     Icon::List,     group: self::ICON_GROUP)
		);

		$event->options->add(
			new IconOption('woocommerce-billing-address',  Icon::ReceiptLong,      __('Billing Address', 'x3p0-breadcrumbs'),  self::ICON_GROUP),
			new IconOption('woocommerce-shipping-address', Icon::Shipping,         __('Shipping Address', 'x3p0-breadcrumbs'), self::ICON_GROUP),
			new IconOption('woocommerce-endpoint',         'core/more-vertical',   __('Endpoint', 'x3p0-breadcrumbs'),         self::ICON_GROUP),
			new IconOption('woocommerce-orderby',          'core/chevron-up-down', __('Product Sorting', 'x3p0-breadcrumbs'),  self::ICON_GROUP)
		);

		$this->addEndpointIconOptions($event);
		$this->addCatalogOrderIconOptions($event);
	}

	/**
	 * Returns the title of the configured shop page, or an empty string when
	 * there isn't one. The `Shop` crumb takes its own label from this same
	 * title, so naming its icon option after it means the block control reads
	 * as whatever the store calls its shop — "Store", "Catalog", a brand name
	 * — rather than a word this plugin picked on the store's behalf. With no
	 * shop page there is no such name to borrow, and the post type archive's
	 * own label is left standing, exactly as the crumb leaves it.
	 */
	private function shopPageTitle(): string
	{
		$shopId = wc_get_page_id('shop');

		return 0 < $shopId ? get_the_title($shopId) : '';
	}

	/**
	 * Moves the options for every WordPress object the store owns into the
	 * extension's group: the product post type, its archive, and every
	 * taxonomy attached to it. Taxonomies are read off the post type rather
	 * than listed, so a store's product attributes (`pa_color` and the rest,
	 * which differ per store) and any a third party registers against products
	 * are gathered up on the same terms as the ones WooCommerce ships. Keys
	 * with no registered option — a taxonomy that isn't publicly viewable —
	 * are passed over by `update()`.
	 */
	private function groupProductObjectOptions(IconOptionsRegistered $event): void
	{
		$keys = [
			IconOptionKey::postType('product'),
			IconOptionKey::postTypeArchive('product')
		];

		foreach (get_object_taxonomies('product') as $taxonomy) {
			$keys[] = IconOptionKey::taxonomy($taxonomy);
		}

		foreach ($keys as $key) {
			$event->options->update($key, group: self::ICON_GROUP);
		}
	}

	/**
	 * Registers a labeled option per endpoint the plugin names, keyed under the
	 * shared `woocommerce-endpoint` option so each carries its own default
	 * without the crumb hardcoding one and can be set on its own in the block
	 * editor. Endpoints WooCommerce or a third party adds that aren't named in
	 * {@see EndpointSlug} have no key of their own and resolve the shared
	 * option instead.
	 */
	private function addEndpointIconOptions(IconOptionsRegistered $event): void
	{
		foreach (EndpointSlug::cases() as $endpoint) {
			$event->options->add(new IconOption(
				$endpoint->optionKey(),
				$endpoint->icon(),
				$endpoint->label(),
				self::ICON_GROUP
			));
		}
	}

	/**
	 * Registers a labeled option per sorting option the plugin names, keyed
	 * under the shared `woocommerce-orderby` option on the same terms as the
	 * endpoints above. The sorting's own name is qualified for the block
	 * editor: "Latest" and "Default" say nothing on their own in a list of
	 * icon settings, where they name the crumb that says a product listing is
	 * sorted that way. Sorting options a third party adds have no key of their
	 * own and resolve the shared option instead.
	 */
	private function addCatalogOrderIconOptions(IconOptionsRegistered $event): void
	{
		foreach (CatalogOrderSlug::cases() as $order) {
			$event->options->add(new IconOption(
				$order->optionKey(),
				$order->icon(),
				sprintf(
					// Translators: %s: Post sorting name, e.g. "Popularity".
					__('Sorted by: %s', 'x3p0-breadcrumbs'),
					$order->label()
				),
				self::ICON_GROUP
			));
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
