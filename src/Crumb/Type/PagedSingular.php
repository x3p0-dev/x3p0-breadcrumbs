<?php

/**
 * Paged singular crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Type;

use WP_Post;
use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Crumb\Crumb;
use X3P0\Breadcrumbs\BreadcrumbsLabel;

/**
 * Crumb representing a sub-page of a multipage singular post (split via the
 * `<!--nextpage-->` tag). Its label is the configured "paged" string filled
 * with the current page number, and its URL is the permalink to that page.
 */
final class PagedSingular extends Crumb
{
	/**
	 * @inheritDoc
	 */
	public function __construct(
		BreadcrumbsContext $context,
		public readonly WP_Post $post
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function getLabel(): string
	{
		return sprintf(
			$this->context->config()->getLabel(BreadcrumbsLabel::Paged),
			number_format_i18n(absint(get_query_var('page')))
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * Borrowed from `_wp_link_page()`.
	 * @link https://developer.wordpress.org/reference/functions/_wp_link_page/
	 */
	public function getUrl(): string
	{
		$page      = get_query_var('page') ? absint(get_query_var('page')) : 1;
		$permalink = (string) get_permalink($this->post);

		if (
			! get_option('permalink_structure')
			|| in_array($this->post->post_status, ['draft', 'pending'], true)
		) {
			return add_query_arg('page', $page, $permalink);
		}

		if (
			'page' === get_option('show_on_front')
			&& (int) get_option('page_on_front') === $this->post->ID
		) {
			return trailingslashit($permalink) . user_trailingslashit(
				$GLOBALS['wp_rewrite']->pagination_base . "/{$page}",
				'single_paged'
			);
		}

		return trailingslashit($permalink) . user_trailingslashit(
			$page,
			'single_paged'
		);
	}
}
