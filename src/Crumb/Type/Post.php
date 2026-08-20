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
use X3P0\Breadcrumbs\Icon\Icon;
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
	 * Resolves this post's icon option per post type.
	 *
	 * @inheritDoc
	 */
	public function iconOptionKey(): string
	{
		return IconOption::postTypeKey($this->post->post_type);
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
	 * Returns an icon for the posts this crumb can say something more useful
	 * about than the generic post type default: one the site owner restricted
	 * access to, the pages WordPress assigns a role to under its settings, and
	 * an attachment whose mime type is one of the media types checked below.
	 * None of them is something an option could be registered against — a post
	 * status, a password, two single pages named in an option, and a mime type
	 * — so all are derived here rather than resolved from the registry. All
	 * stay behind the icon config, since a site owner who picked an icon for
	 * pages or media meant it.
	 *
	 * Restricted access is checked first: whether the post is reachable at all
	 * matters more to the person reading the trail than what kind of place it
	 * is. Both checks report the state that reader is in, so a locked post
	 * stops reading as locked once they unlock it.
	 *
	 * @inheritDoc
	 */
	protected function fallbackIcon(): string
	{
		if ('private' === get_post_status($this->post)) {
			return Icon::Unseen->name();
		}

		if (post_password_required($this->post)) {
			return 'core/key';
		}

		if ($this->isPrivacyPolicy()) {
			return 'core/shield';
		}

		if ($this->isPostsPage()) {
			return Icon::Archive->name();
		}

		if ('attachment' === $this->post->post_type) {
			return $this->attachmentIcon();
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
	 * Returns an icon matching this attachment's mime type, or an empty
	 * string when none of the checked types match — leaving the generic
	 * `attachment` post type default to apply.
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
