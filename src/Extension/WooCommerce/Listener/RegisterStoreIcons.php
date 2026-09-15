<?php

/**
 * WooCommerce icon option listener.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Extension\WooCommerce\Listener;

use X3P0\Breadcrumbs\Extension\WooCommerce\Support\CatalogOrder as CatalogOrderSlug;
use X3P0\Breadcrumbs\Extension\WooCommerce\Support\Endpoint as EndpointSlug;
use X3P0\Breadcrumbs\Extension\WooCommerce\Support\StorePage as StorePageSlug;
use X3P0\Breadcrumbs\Icon\Event\IconOptionsRegistered;
use X3P0\Breadcrumbs\Icon\Icon;
use X3P0\Breadcrumbs\Icon\IconOption;
use X3P0\Breadcrumbs\Icon\IconOptionKey;

/**
 * Registers the store's icon options: the defaults every crumb this extension
 * builds resolves its icon through, and the controls a site owner sets them
 * from in the block editor.
 *
 * The defaults belong here rather than in a crumb's own `getIcon()`, so that a
 * site owner's configured icon still outranks them and another extension can
 * retarget them. Everything lands in the extension's own group, so a site owner
 * finds it together rather than scattered through the catch-all and the post
 * type and taxonomy groups.
 */
final class RegisterStoreIcons
{
	/**
	 * Key of the icon option group everything to do with the store is listed
	 * under in the block editor. It holds this extension's own options and
	 * the options for the WordPress objects the store owns.
	 */
	private const ICON_GROUP = 'woocommerce';

	/**
	 * Two kinds of thing end up in the group. The product post type and its
	 * taxonomies are already registered by the time this runs, so they are
	 * amended with `update()`, which swaps the icon and moves them into the
	 * group while leaving the label and slug the registrar derived from each
	 * object in place: they belong under WooCommerce rather than among the
	 * generic post types and taxonomies.
	 *
	 * The extension's own crumb types have no such counterpart and are added
	 * outright, each under the key its crumb resolves its icon through.
	 */
	public function __invoke(IconOptionsRegistered $event): void
	{
		$event->options->addGroup(self::ICON_GROUP, __('WooCommerce', 'x3p0-breadcrumbs'));

		$event->options->update(IconOptionKey::postType('product'),       icon: Icon::Package);
		$event->options->update(IconOptionKey::taxonomy('product_brand'), icon: Icon::BrandingWatermark);
		$event->options->update(IconOptionKey::taxonomy('product_cat'),   icon: 'core/category');
		$event->options->update(IconOptionKey::taxonomy('product_tag'),   icon: 'core/tag');
		$event->options->update(IconOptionKey::taxonomy('pa_color'),      icon: Icon::Color);
		$event->options->update(IconOptionKey::taxonomy('pa_size'),       icon: Icon::Straighten);

		// The shop *is* the product post type archive — the `Shop`
		// crumb decorates that crumb wherever it appears — so the
		// archive's option is the shop's option. The name follows the
		// crumb's: with a shop page configured, both read as that
		// page's title; without one, the crumb falls back to the post
		// type archive's label, so the option is left to do the same.
		// Passing null leaves it alone.
		$event->options->update(
			IconOptionKey::postTypeArchive('product'),
			icon: 'core/store',
			label: $this->shopPageTitle() ?: null
		);

		$this->groupProductObjectOptions($event);

		$this->addStorePageIconOptions($event);

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
	 * Registers an option per store page, under the key its crumb resolves,
	 * so each carries its own default without the crumb hardcoding one.
	 * Unlike the endpoints and sorting options below, these are
	 * unlabeled: the pages are ordinary pages the site owner picked under
	 * WooCommerce's settings, so an icon for one belongs on the page itself
	 * rather than on a control here. They stay out of the editor and only
	 * carry the default.
	 */
	private function addStorePageIconOptions(IconOptionsRegistered $event): void
	{
		foreach (StorePageSlug::cases() as $page) {
			$event->options->add(new IconOption(
				$page->optionKey(),
				$page->icon(),
				group: self::ICON_GROUP
			));
		}
	}

	/**
	 * Registers a labeled option per endpoint the plugin names, keyed under
	 * the shared `woocommerce-endpoint` option so each carries its own
	 * default without the crumb hardcoding one and can be set on its own in
	 * the block editor. Endpoints WooCommerce or a third party adds that
	 * aren't named in {@see EndpointSlug} have no key of their own and
	 * resolve the shared option instead.
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
	 * icon settings, where they name the crumb that says a product listing
	 * is sorted that way. Sorting options a third party adds have no key of
	 * their own and resolve the shared option instead.
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
}
