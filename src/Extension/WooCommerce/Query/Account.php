<?php

/**
 * WooCommerce account query.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Extension\WooCommerce\Query;

use X3P0\Breadcrumbs\Crumb\CrumbType;

/**
 * Builds the trail for the My Account page and its endpoints (orders,
 * view-order, downloads, edit-address, payment-methods, and the rest), which
 * would otherwise collapse into a single "My Account" crumb. This is the store
 * account rather than a general user profile, so its trail is rooted at the shop
 * like the rest of the store pages.
 */
final class Account extends StorePage
{
	/**
	 * @inheritDoc
	 */
	protected function pageId(): int
	{
		return wc_get_page_id('myaccount');
	}

	/**
	 * @inheritDoc
	 *
	 * The edit-address endpoint has billing and shipping sub-views. When one
	 * is active, the endpoint crumb becomes a linked ancestor (the address
	 * list) and the specific address is added as the leaf. And when the
	 * view-order endpoint is present, add the orders endpoint first to
	 * ensure that it appears in the trail.
	 */
	protected function assembleEndpoint(string $endpoint): void
	{
		if ('view-order' === $endpoint) {
			parent::assembleEndpoint('orders');
		}

		parent::assembleEndpoint($endpoint);

		if ('edit-address' === $endpoint && $type = get_query_var('edit-address')) {
			$this->context->addCrumb(CrumbType::Custom, [
				'label' => $this->addressTitle($type)
			]);
		}
	}

	/**
	 * Returns the localized title for a billing or shipping address sub-view,
	 * matching the heading WooCommerce shows on the edit-address form.
	 *
	 * Note that we must recreate WooCommerce strings and its filter hook
	 * here because it doesn't have a function in its public API for getting
	 * the address title.
	 *
	 * @link https://github.com/woocommerce/woocommerce/issues/66565
	 */
	private function addressTitle(string $type): string
	{
		$type = wc_edit_address_i18n(sanitize_key($type), true);

		return apply_filters(
			'woocommerce_my_account_edit_address_title',
			'shipping' === $type
				? __('Shipping address', 'x3p0-breadcrumbs')
				: __('Billing address', 'x3p0-breadcrumbs'),
			$type
		);
	}
}
