<?php

/**
 * WooCommerce store page query.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Extension\WooCommerce\Query;

use X3P0\Breadcrumbs\Assembler\AssemblerType;
use X3P0\Breadcrumbs\Extension\WooCommerce\WooCommerce;
use X3P0\Breadcrumbs\Query\Query;

/**
 * Base query for a WooCommerce store page — the cart, checkout, and My Account
 * pages. Each is an ordinary page and adds its own crumb. Some of these pages
 * also expose sub-views as endpoints (orders, view-order, order-received, and
 * the rest), which WooCommerce serves as query vars on the host page and which
 * would otherwise collapse into the host page's single crumb; when one is
 * active, a leaf crumb is appended using WooCommerce's own endpoint title.
 * Concrete subclasses supply the host page via `pageId()`.
 */
abstract class StorePage extends Query
{
	/**
	 * Returns the ID of the WooCommerce page that hosts the trail (for
	 * example, the Cart, Checkout, or My Account page).
	 */
	abstract protected function pageId(): int;

	/**
	 * @inheritDoc
	 */
	public function query(): void
	{
		$this->context->assemble(AssemblerType::Home);

		if ($page = get_post($this->pageId())) {
			$this->context->assemble(AssemblerType::Post, [
				'post' => $page
			]);
		}

		// Endpoints (orders, view-order, order-received, …) are query
		// vars on the host page; add crumbs for the active one.
		if ($endpoint = WC()->query?->get_current_endpoint()) {
			$this->assembleEndpoint($endpoint);
		}

		$this->context->assemble(AssemblerType::Paged);
	}

	/**
	 * Adds the crumb(s) for the active endpoint. The base adds a single
	 * leaf crumb using WooCommerce's endpoint title; subclasses may override
	 * to add deeper crumbs for endpoints with their own sub-views.
	 */
	protected function assembleEndpoint(string $endpoint): void
	{
		$this->context->addCrumb(WooCommerce::CRUMB_ENDPOINT, [
			'endpoint' => $endpoint
		]);
	}
}
