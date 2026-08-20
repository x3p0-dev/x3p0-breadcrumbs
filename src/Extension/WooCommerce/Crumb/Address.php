<?php

/**
 * WooCommerce address crumb.
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

/**
 * Crumb representing one of the two sub-views of the account `edit-address`
 * endpoint: the customer's billing or shipping address. Unlike the endpoints
 * themselves, these are not endpoints of their own — they are a value on the
 * `edit-address` query var — so they get a crumb type here rather than being
 * handled by {@see Endpoint}.
 *
 * The type is kept as the raw query var value and normalized on read, since
 * WooCommerce localizes the slug in the URL. Both normalized values have an
 * icon option registered for them (`woocommerce-billing-address` and
 * `woocommerce-shipping-address`), which the slug matches, so the icon resolves
 * through the usual chain with no override needed here.
 */
final class Address extends Crumb
{
	/**
	 * Stores the raw `edit-address` query var value naming the address type,
	 * which may be a localized slug.
	 */
	public function __construct(
		CrumbContext $context,
		public readonly string $type
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function getSlug(): string
	{
		return 'woocommerce-' . $this->addressType() . '-address';
	}

	/**
	 * Returns the localized title for the address sub-view, matching the
	 * heading WooCommerce shows on the edit-address form.
	 *
	 * Note that we must recreate WooCommerce's strings and its filter hook
	 * here because it doesn't have a function in its public API for getting
	 * the address title.
	 *
	 * @link https://github.com/woocommerce/woocommerce/issues/66565
	 *
	 * @inheritDoc
	 */
	public function getLabel(): string
	{
		$type = $this->addressType();

		return apply_filters(
			'woocommerce_my_account_edit_address_title',
			'shipping' === $type
				? __('Shipping address', 'x3p0-breadcrumbs')
				: __('Billing address', 'x3p0-breadcrumbs'),
			$type
		);
	}

	/**
	 * Returns the address type normalized to WooCommerce's own English slug,
	 * `billing` or `shipping`. The slug in the URL is localized, so it is run
	 * back through WooCommerce's translation table first; anything that isn't
	 * shipping is billing, which is how WooCommerce itself treats the value.
	 */
	private function addressType(): string
	{
		return 'shipping' === wc_edit_address_i18n(sanitize_key($this->type), true)
			? 'shipping'
			: 'billing';
	}
}
