<?php

/**
 * Post type assembler.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler\Type;

use WP_Post_Type;
use WP_Rewrite;
use X3P0\Breadcrumbs\Assembler\AbstractAssembler;
use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Crumb\CrumbRegistrar;

/**
 * Assembles breadcrumbs for the given post type.
 */
final class PostType extends AbstractAssembler
{
	/**
	 * @inheritDoc
	 */
	public function __construct(
		protected BreadcrumbsContext $context,
		protected ?WP_Post_Type $postType = null
	) {
		parent::__construct(...func_get_args());
	}

	/**
	 * @inheritDoc
	 *
	 * @global WP_Rewrite $GLOBALS['wp_rewrite']
	 */
	public function assemble(): void
	{
		if (! $this->postType) {
			return;
		}

		// If this the post type is `post`, add the posts page and bail.
		if ('post' === $this->postType->name) {
			$show_on_front = get_option('show_on_front');
			$post_id       = get_option('page_for_posts');

			// Add post crumb if we have a posts page.
			if ('posts' !== $show_on_front && 0 < $post_id) {
				$post = get_post($post_id);

				// If the posts page is the same as the rewrite
				// front path, we should've already handled that
				// scenario at this point.
				if (trim($GLOBALS['wp_rewrite']->front, '/') !== $post->post_name) {
					$this->context->addCrumb(CrumbRegistrar::POST, [
						'post' => $post
					]);
				}
			}

			return;
		}

		// Add post type crumb.
		$this->context->addCrumb(CrumbRegistrar::POST_TYPE, [
			'postType' => $this->postType
		]);
	}
}
