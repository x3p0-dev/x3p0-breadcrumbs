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

use WP_Post;
use X3P0\Breadcrumbs\Assembler\AssemblerType;
use X3P0\Breadcrumbs\Crumb\CrumbType;
use X3P0\Breadcrumbs\Query\Query;

/**
 * Base query for a WooCommerce store page — the cart, checkout, and My Account
 * pages. Each is an ordinary page that belongs to the store, so the trail is
 * rooted at the shop (unless the shop page is the front page, where the home
 * crumb already represents it) and then adds the page's own crumb. Some of these
 * pages also expose sub-views as endpoints (orders, view-order, order-received,
 * and the rest), which WooCommerce serves as query vars on the host page and
 * which would otherwise collapse into the host page's single crumb; when one is
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

		// Root the trail at the shop, since these pages belong to the
		// store. The product post type archive is the shop (see the
		// WooCommerce PostType crumb). Skip it when the shop page is
		// the front page so it is not duplicated by the home crumb.
		if (! $this->shopIsFrontPage()) {
			$this->context->addCrumb(CrumbType::PostType, [
				'postType' => get_post_type_object('product')
			]);
		}

		$page = get_post($this->pageId());

		if ($page instanceof WP_Post) {
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
		$this->context->addCrumb(CrumbType::Custom, [
			'label' => WC()->query->get_endpoint_title($endpoint),
			'url'   => wc_get_endpoint_url($endpoint)
		]);
	}

	/**
	 * Whether the shop page is set as the site's static front page.
	 */
	private function shopIsFrontPage(): bool
	{
		return 'page' === get_option('show_on_front')
			&& (int) get_option('page_on_front') === wc_get_page_id('shop');
	}
}
