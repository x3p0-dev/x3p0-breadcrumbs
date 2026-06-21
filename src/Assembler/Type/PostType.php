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
use X3P0\Breadcrumbs\Assembler\Assembler;
use X3P0\Breadcrumbs\Assembler\AssemblerType;
use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Crumb\CrumbType;

/**
 * Assembles breadcrumbs for the given post type.
 */
final class PostType extends Assembler
{
	/**
	 * @inheritDoc
	 */
	public function __construct(
		BreadcrumbsContext $context,
		protected ?WP_Post_Type $postType = null
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 *
	 * @global WP_Rewrite $GLOBALS['wp_rewrite']
	 */
	public function assemble(): void
	{
		// Bail early if there is no post type or if the post type
		// already exists in the crumb collection.
		if (! $this->postType || $this->postTypeCrumbExists()) {
			return;
		}

		// If this the post type is `post`, add the posts page and bail.
		if ('post' === $this->postType->name) {
			$show_on_front = get_option('show_on_front');
			$post_id       = absint(get_option('page_for_posts'));

			// Add post crumb if we have a posts page.
			if ('posts' !== $show_on_front && 0 < $post_id) {
				$this->context->assemble(AssemblerType::Post, [
					'post' => get_post($post_id)
				]);
			}

			return;
		}

		// Add post type crumb.
		$this->context->addCrumb(CrumbType::PostType, [
			'postType' => $this->postType
		]);
	}

	/**
	 * Checks if the current post type already exists in the crumb collection.
	 */
	private function postTypeCrumbExists(): bool
	{
		return $this->context->crumbs()->hasWhere(
			key:      CrumbType::PostType->value,
			property: 'postType',
			callback: fn(WP_Post_Type $postType) => $postType->name === $this->postType->name
		);
	}
}
