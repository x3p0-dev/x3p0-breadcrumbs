<?php

/**
 * WooCommerce icon listener.
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
use X3P0\Breadcrumbs\Icon\Event\IconPresetsRegistered;
use X3P0\Breadcrumbs\Icon\Icon;
use X3P0\Breadcrumbs\Icon\IconPreset;
use X3P0\Breadcrumbs\Icon\IconPresets;
use X3P0\Breadcrumbs\Icon\IconPresetKey;

/**
 * Says what the store's crumbs show and what a site owner sets them from.
 *
 * The icons belong here rather than on the crumbs themselves, so that a site
 * owner's configured icon still outranks them and another extension can
 * retarget them. Everything lands in the extension's own group, so a site
 * owner finds it together rather than scattered through the catch-all and the
 * post type and taxonomy groups.
 */
final class RegisterIcons
{
	/**
	 * Key of the group everything to do with the store is listed under in
	 * the block editor. It holds this extension's own controls and those for
	 * the WordPress objects the store owns.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const ICON_GROUP = 'woocommerce';

	/**
	 * Registers the store's icons and the controls for setting them.
	 */
	public function __invoke(IconPresetsRegistered $event): void
	{
		$presets = $event->presets;

		$presets->registerGroup(self::ICON_GROUP, __('WooCommerce', 'x3p0-breadcrumbs'));

		$this->registerProductObjects($presets);
		$this->registerStoreCrumbs($presets);

		$this->groupProductObjectOptions($presets);
		$this->nameShopOption($presets);
	}

	/**
	 * Retargets the icons of the WordPress objects the store owns. These are
	 * amended rather than registered: the post type and its taxonomies
	 * already derive a preset, label and all, and only the icon needs
	 * changing. Amending also means a taxonomy a given store doesn't have is
	 * passed over rather than conjured into existence.
	 */
	private function registerProductObjects(IconPresets $presets): void
	{
		$icons = [
			IconPresetKey::postType('product')        => Icon::Package,
			IconPresetKey::postTypeArchive('product') => 'core/store',
			IconPresetKey::taxonomy('product_brand')  => Icon::BrandingWatermark,
			IconPresetKey::taxonomy('product_cat')    => 'core/category',
			IconPresetKey::taxonomy('product_tag')    => 'core/tag',
			IconPresetKey::taxonomy('pa_color')       => Icon::Color,
			IconPresetKey::taxonomy('pa_size')        => Icon::Straighten
		];

		foreach ($icons as $key => $icon) {
			$presets->amend($key, static fn (IconPreset $preset) => $preset->withIcon($icon));
		}
	}

	/**
	 * Registers the crumbs this extension builds itself, one call per key,
	 * since a preset says everything there is to say about one.
	 *
	 * Store pages are the exception and carry no label, which keeps them out
	 * of the block editor: they are ordinary pages the site owner picked
	 * under WooCommerce's settings, so an icon for one belongs on the page
	 * itself rather than on a control here.
	 */
	private function registerStoreCrumbs(IconPresets $presets): void
	{
		$presets->register('woocommerce-billing-address', new IconPreset(
			Icon::ReceiptLong,
			__('Billing Address', 'x3p0-breadcrumbs'),
			self::ICON_GROUP
		));

		$presets->register('woocommerce-shipping-address', new IconPreset(
			Icon::Shipping,
			__('Shipping Address', 'x3p0-breadcrumbs'),
			self::ICON_GROUP
		));

		$presets->register('woocommerce-endpoint', new IconPreset(
			'core/more-vertical',
			__('Endpoint', 'x3p0-breadcrumbs'),
			self::ICON_GROUP
		));

		$presets->register('woocommerce-orderby', new IconPreset(
			'core/chevron-up-down',
			__('Product Sorting', 'x3p0-breadcrumbs'),
			self::ICON_GROUP
		));

		foreach (StorePageSlug::cases() as $page) {
			$presets->register($page, new IconPreset($page->icon()));
		}

		foreach (EndpointSlug::cases() as $endpoint) {
			$presets->register($endpoint, new IconPreset(
				$endpoint->icon(),
				$endpoint->label(),
				self::ICON_GROUP
			));
		}

		foreach (CatalogOrderSlug::cases() as $order) {
			$presets->register($order, new IconPreset(
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
	 * Moves the controls for every WordPress object the store owns into the
	 * extension's group: the product post type, its archive, and every
	 * taxonomy attached to it. Taxonomies are read off the post type rather
	 * than listed, so a store's product attributes (`pa_color` and the rest,
	 * which differ per store) and any a third party registers against
	 * products are gathered up on the same terms as the ones WooCommerce
	 * ships. A key with no control — a taxonomy that isn't publicly viewable
	 * — is passed over by `amend()` itself.
	 */
	private function groupProductObjectOptions(IconPresets $presets): void
	{
		$keys = [
			IconPresetKey::postType('product'),
			IconPresetKey::postTypeArchive('product')
		];

		foreach (get_object_taxonomies('product') as $taxonomy) {
			$keys[] = IconPresetKey::taxonomy($taxonomy);
		}

		foreach ($keys as $key) {
			$presets->amend($key, static fn (IconPreset $preset) => $preset->withGroup(self::ICON_GROUP));
		}
	}

	/**
	 * Points the product archive's control at the shop. The shop *is* the
	 * product post type archive — the `Shop` crumb decorates that crumb
	 * wherever it appears — so the archive's control is the shop's control.
	 * The name follows the crumb's: with a shop page configured, both read
	 * as that page's title; without one, the crumb falls back to the post
	 * type archive's label, so the control is left to do the same.
	 */
	private function nameShopOption(IconPresets $presets): void
	{
		$title = $this->shopPageTitle();

		if ('' === $title) {
			return;
		}

		$presets->amend(
			IconPresetKey::postTypeArchive('product'),
			static fn (IconPreset $preset) => $preset->withLabel($title)
		);
	}

	/**
	 * Returns the title of the configured shop page, or an empty string when
	 * there isn't one. The `Shop` crumb takes its own label from this same
	 * title, so naming its icon control after it means the control reads as
	 * whatever the store calls its shop — "Store", "Catalog", a brand name —
	 * rather than a word this plugin picked on the store's behalf.
	 */
	private function shopPageTitle(): string
	{
		$shopId = wc_get_page_id('shop');

		return 0 < $shopId ? get_the_title($shopId) : '';
	}
}
