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
	 * Stores the post this crumb represents.
	 */
	public function __construct(
		CrumbContext $context,
		#[NoAutowire] public readonly WP_Post $post
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
	 * Resolves this post's icon option per post type, except where the site
	 * owner restricted access to it or it is a piece of media. Being private,
	 * locked, an image, or a video is a state any post of its type can be in
	 * rather than a fact about a particular one, so none of them can be named
	 * per-post and each gets an option to be configured for all of them at
	 * once.
	 *
	 * Restricted access is checked before the kind of file: whether the post
	 * is reachable at all matters more to the person reading the trail than
	 * what sort of place it is. Both of those checks report the state that
	 * reader is in, so a locked post stops reading as locked once they unlock
	 * it, and the private check leads.
	 *
	 * The pages WordPress assigns a role to under its settings are not here.
	 * Each is one particular page, which its own icon meta names directly, so
	 * they only carry a derived default — see `fallbackIcon()`.
	 *
	 * @inheritDoc
	 */
	public function iconOptionKey(): string
	{
		return match (true) {
			'private' === get_post_status($this->post) => 'private-post',
			post_password_required($this->post)        => 'protected-post',
			'attachment' === $this->post->post_type    => $this->mediaOptionKey(),
			default => IconOption::postTypeKey($this->post->post_type)
		};
	}

	/**
	 * Returns the icon stored in this post's own meta — the site owner's
	 * editorial choice for this exact post, so it outranks the icon they
	 * configured for the post type.
	 *
	 * @inheritDoc
	 */
	protected function explicitIcon(): string
	{
		return (string) get_post_meta($this->post->ID, MetaKey::Icon->value, true);
	}

	/**
	 * Returns an icon for the pages WordPress assigns a role to under its
	 * settings. Each is one particular page, so it gets no block control of
	 * its own — a page's icon belongs to that page's own icon meta — and
	 * resolving here rather than through `iconOptionKey()` is what keeps them
	 * behind the icon config, since a site owner who picked an icon for pages
	 * meant it. The icons themselves are read from the registry like every
	 * other icon the plugin renders, under options registered without a label.
	 *
	 * Nothing else resolves here. Restricted access and the kind of file a
	 * piece of media is are states any post can be in, so they get options of
	 * their own — see `iconOptionKey()`.
	 *
	 * @inheritDoc
	 */
	protected function fallbackIcon(): string
	{
		if ($this->isPrivacyPolicy()) {
			return $this->context->iconOptions->icon('privacy-policy');
		}

		if ($this->isPostsPage()) {
			return $this->context->iconOptions->icon('posts-page');
		}

		return '';
	}

	/**
	 * Whether this post is the page assigned as the site's privacy policy.
	 * Matched by ID against the option rather than with `is_privacy_policy()`,
	 * which only answers for the queried object — the page can also appear in
	 * the trail as an ancestor of one of its children, and it should read the
	 * same either way.
	 */
	private function isPrivacyPolicy(): bool
	{
		$pageId = absint(get_option('wp_page_for_privacy_policy'));

		return 0 < $pageId && $pageId === $this->post->ID;
	}

	/**
	 * Whether this post is the page assigned to list the site's blog posts,
	 * and that assignment is actually in effect — WordPress keeps the option's
	 * value when the front page is switched back to showing posts, at which
	 * point the page is just a page again.
	 *
	 * The page stays an ordinary page as far as the icon option goes, which is
	 * what WordPress makes it; it only borrows the archive icon as a default,
	 * since listing posts is what it does.
	 */
	private function isPostsPage(): bool
	{
		$pageId = absint(get_option('page_for_posts'));

		return 'posts' !== get_option('show_on_front')
			&& 0 < $pageId
			&& $pageId === $this->post->ID;
	}

	/**
	 * Returns the option key for the kind of media this attachment is, or the
	 * attachment post type's own key when it is none of them — a PDF, an
	 * archive — which is the catch-all the media group carries for exactly
	 * that. Mime types are matched with `wp_attachment_is()` rather than read
	 * off `post_mime_type`, so the same rules WordPress applies elsewhere
	 * decide what counts as an image, an audio file, or a video.
	 */
	private function mediaOptionKey(): string
	{
		return match (true) {
			wp_attachment_is('image', $this->post) => 'media-image',
			wp_attachment_is('audio', $this->post) => 'media-audio',
			wp_attachment_is('video', $this->post) => 'media-video',
			default => IconOption::postTypeKey('attachment')
		};
	}
}
