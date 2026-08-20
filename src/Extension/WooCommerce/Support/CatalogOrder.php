<?php

/**
 * WooCommerce catalog order enum.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Extension\WooCommerce\Support;

/**
 * The catalog sorting options WooCommerce offers on the shop, product taxonomy
 * archives, and product searches, each named by the `orderby` request var. This
 * is the single source of truth for the ones this plugin has an opinion about,
 * shared by the `CatalogOrder` crumb and the extension registering their icon
 * options, in the same way {@see Endpoint} is for the account endpoints.
 *
 * WooCommerce builds the option labels inline in `woocommerce_catalog_ordering()`
 * and echoes them straight into a `<select>`, with no way to ask for them: its
 * own Catalog Sorting block output-buffers that function and rewrites the HTML.
 * Buffering it here is not an option either, since it bails unless the shop
 * loop props are set up, and merely reading them sets them — which would fix
 * the wrong values in place for the real loop rendered later. So the labels are
 * recreated below and run back through WooCommerce's own filter, the same way
 * {@see \X3P0\Breadcrumbs\Extension\WooCommerce\Crumb\Address} recreates the
 * address titles, so a plugin that renames, adds, or removes a sorting option
 * is still honored.
 */
enum CatalogOrder: string
{
	case MenuOrder  = 'menu_order';
	case Popularity = 'popularity';
	case Rating     = 'rating';
	case Date       = 'date';
	case Price      = 'price';
	case PriceDesc  = 'price-desc';
	case Relevance  = 'relevance';

	/**
	 * Returns the sorting option the current request asks for, or an empty
	 * string when it asks for none. The value is read from the request
	 * rather than from `get_query_var('orderby')`, because WooCommerce
	 * overwrites that var on `pre_get_posts` with the resolved WordPress
	 * ordering (`meta_value_num` and the like), so by the time a trail is
	 * built the original value is gone. It is returned as a raw string,
	 * since a third party may register options this enum has no case for.
	 */
	public static function requested(): string
	{
		// This is a read-only display value, so it is sanitized rather
		// than nonce-checked. It is sanitized with `wc_clean()`, which
		// is WooCommerce's own recursive `sanitize_text_field()` and
		// therefore handles the array form of the var below, but which
		// the sniff has no way of knowing is a sanitizing function.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$requested = wc_clean(wp_unslash($_GET['orderby'] ?? ''));

		// WooCommerce accepts the var as an array and takes the first
		// value from it, so do the same.
		if (is_array($requested)) {
			$requested = reset($requested);
		}

		return is_string($requested) ? $requested : '';
	}

	/**
	 * Returns the sorting options as a map of request value to label, run
	 * through WooCommerce's own filter so a plugin that renames, adds, or
	 * removes an option is honored here too. An option missing from the map
	 * is one the store does not offer, which is why the crumb is not built
	 * for it.
	 *
	 * @return array<string, string>
	 */
	public static function labels(): array
	{
		$labels = [];

		foreach (self::cases() as $case) {
			$labels[$case->value] = $case->label();
		}

		// phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
		return apply_filters('woocommerce_catalog_orderby', $labels);
	}

	/**
	 * Returns the label for the option, matching the wording WooCommerce
	 * uses for its own short-form sorting labels.
	 */
	public function label(): string
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return match ($this) {
			self::MenuOrder  => __('Default', 'x3p0-breadcrumbs'),
			self::Popularity => __('Popularity', 'x3p0-breadcrumbs'),
			self::Rating     => __('Average rating', 'x3p0-breadcrumbs'),
			self::Date       => __('Latest', 'x3p0-breadcrumbs'),
			self::Price      => __('Price: low to high', 'x3p0-breadcrumbs'),
			self::PriceDesc  => __('Price: high to low', 'x3p0-breadcrumbs'),
			self::Relevance  => __('Relevance', 'x3p0-breadcrumbs')
		};
	}

	/**
	 * Returns the icon option key for this sorting option. Every sorting
	 * crumb shares the one `woocommerce-orderby` slug, which is all an
	 * option registered by a third party has to resolve an icon from; the
	 * options named here get a key apiece under it, so each carries its own
	 * registered default rather than the crumb hardcoding one.
	 */
	public function optionKey(): string
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return 'woocommerce-orderby:' . $this->value;
	}

	/**
	 * Returns the default icon registered for this option's key.
	 */
	public function icon(): string
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return match ($this) {
			self::MenuOrder  => 'core/menu',
			self::Popularity,
			self::Rating     => 'core/star-filled',
			self::Date       => 'core/scheduled',
			self::Price      => 'core/arrow-up',
			self::PriceDesc  => 'core/arrow-down',
			self::Relevance  => 'core/search'
		};
	}
}
