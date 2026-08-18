<?php

/**
 * WooCommerce endpoint assembler.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Extension\WooCommerce\Assembler;

use X3P0\Breadcrumbs\Assembler\Assembler;
use X3P0\Breadcrumbs\Assembler\AssemblerContext;
use X3P0\Breadcrumbs\Crumb\CrumbType;
use X3P0\Breadcrumbs\Extension\WooCommerce\Crumb\Endpoint as EndpointCrumb;
use X3P0\Breadcrumbs\Extension\WooCommerce\Support\Endpoint as EndpointSlug;

/**
 * Adds the crumb(s) for the active WooCommerce endpoint (orders, view-order,
 * order-received, edit-address, payment-methods, and the rest), which
 * WooCommerce serves as query vars on a store page and which would otherwise
 * collapse into that page's single crumb. Most endpoints only need a single
 * leaf crumb using WooCommerce's own endpoint title; the few that need more
 * are special-cased here, giving new endpoints one place to grow into. See
 * {@see EndpointSlug} for why the endpoint slugs themselves are an enum
 * rather than magic strings or constants declared here.
 */
final class Endpoint extends Assembler
{
	/**
	 * @inheritDoc
	 */
	public function __construct(
		AssemblerContext $context,
		private readonly string $endpoint
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function assemble(): void
	{
		match (EndpointSlug::tryFrom($this->endpoint)) {
			EndpointSlug::ViewOrder   => $this->assembleViewOrder(),
			EndpointSlug::EditAddress => $this->assembleEditAddress(),
			default                   => $this->addEndpointCrumb($this->endpoint)
		};
	}

	/**
	 * The view-order endpoint is nested under orders; add that endpoint
	 * first so it appears as an ancestor in the trail.
	 */
	private function assembleViewOrder(): void
	{
		$this->addEndpointCrumb(EndpointSlug::Orders->value);
		$this->addEndpointCrumb($this->endpoint);
	}

	/**
	 * The edit-address endpoint has billing and shipping sub-views. Add the
	 * endpoint crumb as a linked ancestor, then append the specific address
	 * type as the leaf.
	 */
	private function assembleEditAddress(): void
	{
		$this->addEndpointCrumb($this->endpoint);

		if ($type = get_query_var(EndpointSlug::EditAddress->value)) {
			$this->context->addCrumb(CrumbType::Custom, [
				'label' => $this->addressTitle($type)
			]);
		}
	}

	/**
	 * Adds a single leaf crumb for the given endpoint using WooCommerce's
	 * own endpoint title.
	 */
	private function addEndpointCrumb(string $endpoint): void
	{
		$this->context->addCrumb(EndpointCrumb::class, [
			'endpoint' => $endpoint
		]);
	}

	/**
	 * Returns the localized title for a billing or shipping address
	 * sub-view, matching the heading WooCommerce shows on the edit-address
	 * form.
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
