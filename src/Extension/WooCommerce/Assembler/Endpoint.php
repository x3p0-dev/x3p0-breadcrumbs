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
use X3P0\Breadcrumbs\Extension\WooCommerce\Crumb\Address as AddressCrumb;
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
	 * type as the leaf. The address crumb takes the raw query var value and
	 * normalizes it itself, since the slug WooCommerce puts in the URL is
	 * localized.
	 */
	private function assembleEditAddress(): void
	{
		$this->addEndpointCrumb($this->endpoint);

		if ($type = get_query_var(EndpointSlug::EditAddress->value)) {
			$this->context->addCrumb(AddressCrumb::class, [
				'type' => $type
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
}
