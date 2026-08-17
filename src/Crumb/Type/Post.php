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
use X3P0\Breadcrumbs\BreadcrumbsConfig;
use X3P0\Breadcrumbs\BreadcrumbsLabel;
use X3P0\Breadcrumbs\Crumb\Crumb;
use X3P0\Breadcrumbs\Meta\MetaKey;
use X3P0\Breadcrumbs\Packages\Framework\Container\Attributes\NoAutowire;

/**
 * Crumb representing a single post (of any post type). Its label is the post
 * title, falling back to the configured "untitled" string, and its URL is the
 * post permalink.
 */
final class Post extends Crumb
{
	/**
	 * @inheritDoc
	 */
	protected const ICON = 'core/pencil';

	/**
	 * @inheritDoc
	 */
	public function __construct(
		BreadcrumbsConfig $config,
		#[NoAutowire] public readonly WP_Post $post
	) {
		parent::__construct(config: $config);
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
	 * Returns this post's own icon override (post meta) when one is set;
	 * otherwise, for an attachment, a media-type-aware icon when its mime
	 * type matches one; otherwise the post type's configured default;
	 * otherwise the shared fallback from `Crumb::DEFAULT_ICON`.
	 *
	 * @inheritDoc
	 */
	public function getIcon(): string
	{
		$icon = get_post_meta($this->post->ID, MetaKey::Icon->value, true);

		if ('' !== $icon) {
			return $icon;
		}

		if ('attachment' === $this->post->post_type && $icon = $this->attachmentIcon()) {
			return $icon;
		}

		return $this->config->getPostTypeIcon($this->post->post_type) ?: self::ICON;
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
