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
use X3P0\Breadcrumbs\Icon\IconPresetDefinition;
use X3P0\Breadcrumbs\Icon\IconPresetKey;
use X3P0\Breadcrumbs\Packages\Framework\Container\Attributes\NoAutowire;

/**
 * Crumb representing a single post (of any post type). Its label is the post
 * title, falling back to the configured "untitled" string, and its URL is the
 * post permalink.
 */
final class Post extends Crumb
{
	/**
	 * Stores the post this crumb represents.
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
	 * @inheritDoc
	 */
	public function getIconPresetKey(): IconPresetDefinition|string
	{
		return match (true) {
			$this->isPrivacyPolicy()                => IconPresetKey::PrivacyPolicy,
			$this->isPostsPage()                    => IconPresetKey::PostsPage,
			'attachment' === $this->post->post_type => $this->mediaPresetKey(),
			default                                 => IconPresetKey::postType($this->post->post_type)
		};
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon(): string
	{
		return (string) get_post_meta($this->post->ID, MetaKey::Icon->value, true);
	}

	/**
	 * Whether this post is the page assigned as the site's privacy policy.
	 */
	private function isPrivacyPolicy(): bool
	{
		$pageId = absint(get_option('wp_page_for_privacy_policy'));

		return 0 < $pageId && $pageId === $this->post->ID;
	}

	/**
	 * Whether this post is the page assigned to list the site's blog posts.
	 */
	private function isPostsPage(): bool
	{
		$pageId = absint(get_option('page_for_posts'));

		return 'posts' !== get_option('show_on_front')
			&& 0 < $pageId
			&& $pageId === $this->post->ID;
	}

	/**
	 * Returns the preset key for the kind of media this attachment is.
	 */
	private function mediaPresetKey(): IconPresetDefinition|string
	{
		return match (true) {
			wp_attachment_is('image', $this->post) => IconPresetKey::MediaImage,
			wp_attachment_is('audio', $this->post) => IconPresetKey::MediaAudio,
			wp_attachment_is('video', $this->post) => IconPresetKey::MediaVideo,
			default                                => IconPresetKey::postType('attachment')
		};
	}
}
