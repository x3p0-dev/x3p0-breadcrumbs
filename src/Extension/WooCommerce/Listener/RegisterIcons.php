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
use X3P0\Breadcrumbs\Icon\IconOptionGroup;
use X3P0\Breadcrumbs\Icon\IconOptionKey;
use X3P0\Breadcrumbs\Icon\IconOptionRegistry;

/**
 * Registers the store's icon options: the defaults every crumb this extension
 * builds resolves its icon through, and the controls a site owner sets them
 * from in the block editor.
 *
 * The defaults belong here rather than on the crumbs themselves, so that a
 * site owner's configured icon still outranks them and another extension can
 * retarget them. Everything lands in the extension's own group, so a site owner
 * finds it together rather than scattered through the catch-all and the post
 * type and taxonomy groups.
 */
final class RegisterIcons
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
	 * replaced with a copy that changes only the icon or group, keeping the
	 * label and slug the registrar derived from each object. The extension's
	 * own crumb types have no such counterpart and are registered outright,
	 * each under the key its crumb resolves its icon through.
	 */
	public function __invoke(IconOptionsRegistered $event): void
	{
		$options = $event->options;

		$event->groups->register(
			self::ICON_GROUP,
			new IconOptionGroup(__('WooCommerce', 'x3p0-breadcrumbs'))
		);

		$this->setProductObjectIcons($options);
		$this->setShopIcon($options);
		$this->groupProductObjectOptions($options);

		$options->register('woocommerce-billing-address', new IconOption(Icon::ReceiptLong, __('Billing Address', 'x3p0-breadcrumbs'), self::ICON_GROUP));
		$options->register('woocommerce-shipping-address', new IconOption(Icon::Shipping, __('Shipping Address', 'x3p0-breadcrumbs'), self::ICON_GROUP));
		$options->register('woocommerce-endpoint', new IconOption('core/more-vertical', __('Endpoint', 'x3p0-breadcrumbs'), self::ICON_GROUP));
		$options->register('woocommerce-orderby', new IconOption('core/chevron-up-down', __('Product Sorting', 'x3p0-breadcrumbs'), self::ICON_GROUP));

		$this->addStorePageIconOptions($options);
		$this->addEndpointIconOptions($options);
		$this->addCatalogOrderIconOptions($options);
	}

	/**
	 * Retargets the icons of the WordPress objects the store owns. Only
	 * objects with an option already registered are touched: the product
	 * attribute taxonomies differ per store, and `product_brand` arrived in a
	 * later WooCommerce release, so several of these keys name a thing a
	 * given site may not have. Registering one outright would conjure an
	 * option for a taxonomy that isn't there.
	 */
	private function setProductObjectIcons(IconOptionRegistry $options): void
	{
		$icons = [
			IconOptionKey::postType('product')       => Icon::Package,
			IconOptionKey::taxonomy('product_brand') => Icon::BrandingWatermark,
			IconOptionKey::taxonomy('product_cat')   => 'core/category',
			IconOptionKey::taxonomy('product_tag')   => 'core/tag',
			IconOptionKey::taxonomy('pa_color')      => Icon::Color,
			IconOptionKey::taxonomy('pa_size')       => Icon::Straighten
		];

		foreach ($icons as $key => $icon) {
			if ($option = $options->get($key)) {
				$options->replace($key, $option->withIcon($icon));
			}
		}
	}

	/**
	 * Points the product archive's option at the shop. The shop *is* the
	 * product post type archive — the `Shop` crumb decorates that crumb
	 * wherever it appears — so the archive's option is the shop's option. The
	 * name follows the crumb's: with a shop page configured, both read as that
	 * page's title; without one, the crumb falls back to the post type
	 * archive's label, so the option is left to do the same.
	 */
	private function setShopIcon(IconOptionRegistry $options): void
	{
		$key = IconOptionKey::postTypeArchive('product');

		if (! $option = $options->get($key)) {
			return;
		}

		$option = $option->withIcon('core/store');
		$title  = $this->shopPageTitle();

		$options->replace($key, '' !== $title ? $option->withLabel($title) : $option);
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
	 * are passed over, for the reason given in `setProductObjectIcons()`.
	 */
	private function groupProductObjectOptions(IconOptionRegistry $options): void
	{
		$keys = [
			IconOptionKey::postType('product'),
			IconOptionKey::postTypeArchive('product')
		];

		foreach (get_object_taxonomies('product') as $taxonomy) {
			$keys[] = IconOptionKey::taxonomy($taxonomy);
		}

		foreach ($keys as $key) {
			if ($option = $options->get($key)) {
				$options->replace($key, $option->withGroup(self::ICON_GROUP));
			}
		}
	}

	/**
	 * Registers an option per store page, under the key its crumb resolves,
	 * so each carries its own default without the crumb hardcoding one.
	 * Unlike the endpoints and sorting options below, these are unlabeled:
	 * the pages are ordinary pages the site owner picked under WooCommerce's
	 * settings, so an icon for one belongs on the page itself rather than on
	 * a control here. They stay out of the editor and only carry the default.
	 */
	private function addStorePageIconOptions(IconOptionRegistry $options): void
	{
		foreach (StorePageSlug::cases() as $page) {
			$options->register(
				$page->optionKey(),
				new IconOption($page->icon(), group: self::ICON_GROUP)
			);
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
	private function addEndpointIconOptions(IconOptionRegistry $options): void
	{
		foreach (EndpointSlug::cases() as $endpoint) {
			$options->register(
				$endpoint->optionKey(),
				new IconOption($endpoint->icon(), $endpoint->label(), self::ICON_GROUP)
			);
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
	private function addCatalogOrderIconOptions(IconOptionRegistry $options): void
	{
		foreach (CatalogOrderSlug::cases() as $order) {
			$options->register(
				$order->optionKey(),
				new IconOption(
					$order->icon(),
					sprintf(
						// Translators: %s: Post sorting name, e.g. "Popularity".
						__('Sorted by: %s', 'x3p0-breadcrumbs'),
						$order->label()
					),
					self::ICON_GROUP
				)
			);
		}
	}
}
