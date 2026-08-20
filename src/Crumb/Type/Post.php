<?php

/**
 * Post crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Type;

use WP_Post;
use X3P0\Breadcrumbs\BreadcrumbsLabel;
use X3P0\Breadcrumbs\Crumb\Crumb;
use X3P0\Breadcrumbs\Crumb\CrumbContext;
use X3P0\Breadcrumbs\Meta\MetaKey;
use X3P0\Breadcrumbs\Icon\IconOption;
use X3P0\Breadcrumbs\Packages\Framework\Container\Attributes\NoAutowire;

/**
 * Crumb representing a single post (of any post type). Its label is the post
 * title, falling back to the configured "untitled" string, and its URL is the
 * post permalink.
 */
final class Post extends Crumb
{
	/**
	 * Stores the post and an optional icon override — a domain-specific
	 * default for a page with no post-type-level signal of its own, such as
	 * WooCommerce's Cart or Checkout page (ordinary `page`-type posts), set
	 * by a `CrumbsBuilt` listener that rebuilds the crumb via
	 * `replaceInstanceWhere()` (see `Extension\WooCommerce::onCrumbsBuilt()`
	 * for the pattern). Never set by the assembler that builds this crumb in
	 * the first place — trail assembly shouldn't need to know about icons.
	 */
	public function __construct(
		CrumbContext $context,
		#[NoAutowire] public readonly WP_Post $post,
		private readonly string $icon = ''
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function getSlug(): string
	{
		return 'post';
	}

	/**
	 * @inheritDoc
	 */
	public function getLabel(): string
	{
		$postId = $this->post->ID;

		if (is_single($postId) || is_page($postId) || is_attachment($postId)) {
			return single_post_title('', false) ?: $this->config->getLabel(BreadcrumbsLabel::Untitled);
		}

		return get_the_title($this->post->ID) ?: $this->config->getLabel(BreadcrumbsLabel::Untitled);
	}

	/**
	 * @inheritDoc
	 */
	public function getUrl(): string
	{
		return (string) get_permalink($this->post->ID);
	}

	/**
	 * Resolves this post's icon option per post type.
	 *
	 * @inheritDoc
	 */
	public function iconOptionKey(): string
	{
		return IconOption::postTypeKey($this->post->post_type);
	}

	/**
	 * Returns this post's own icon override (post meta) when one is set —
	 * the site owner's explicit editorial choice, so it wins over everything
	 * else; otherwise this crumb's own instance-level override, if one was
	 * set (e.g., WooCommerce's Cart page); otherwise, for an attachment, a
	 * media-type-aware icon when its mime type matches one; otherwise the
	 * post type's icon option resolved by the parent.
	 *
	 * @inheritDoc
	 */
	public function getIcon(): string
	{
		$icon = get_post_meta($this->post->ID, MetaKey::Icon->value, true);

		if ('' !== $icon) {
			return $icon;
		}

		if ('' !== $this->icon) {
			return $this->icon;
		}

		if ('attachment' === $this->post->post_type && $icon = $this->attachmentIcon()) {
			return $icon;
		}

		return parent::getIcon();
	}

	/**
	 * Returns an icon matching this attachment's mime type, or an empty
	 * string when none of the checked types match — leaving the caller to
	 * fall back to the generic `attachment` post type default.
	 */
	private function attachmentIcon(): string
	{
		return match (true) {
			wp_attachment_is('image', $this->post) => 'core/image',
			wp_attachment_is('audio', $this->post) => 'core/audio',
			wp_attachment_is('video', $this->post) => 'core/capture-video',
			default                                => ''
		};
	}
}
