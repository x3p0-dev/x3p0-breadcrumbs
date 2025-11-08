<?php

/**
 * Post type archive query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query\Type;

use WP_Post_Type;
use WP_User;
use X3P0\Breadcrumbs\Assembler\AssemblerRegistrar;
use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Crumb\CrumbRegistrar;
use X3P0\Breadcrumbs\Query\AbstractQuery;

final class PostTypeArchive extends AbstractQuery
{
	/**
	 * @inheritDoc
	 */
	public function __construct(
		BreadcrumbsContext $context,
		protected ?WP_Post_Type $postType = null,
		protected ?WP_User $user = null
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function query(): void
	{
		$type = $this->postType ?: get_post_type_object(get_query_var('post_type'));

		$this->context->assemble(AssemblerRegistrar::HOME);

		if (false !== $type->rewrite) {
			// Build rewrite front crumbs if post type uses it.
			if ($type->rewrite['with_front']) {
				$this->context->assemble(AssemblerRegistrar::REWRITE_FRONT);
			}

			// If there's a rewrite slug, check for parents.
			if (! empty($type->rewrite['slug'])) {
				$this->context->assemble(AssemblerRegistrar::PATH, [
					'path' => $type->rewrite['slug']
				]);
			}
		}

		// Add post type crumb if not already in the crumbs collection.
		if (! $this->context->crumbs()->has(CrumbRegistrar::POST_TYPE)) {
			$this->context->addCrumb(CrumbRegistrar::POST_TYPE, [
				'postType' => $type
			]);
		}

		// If viewing a post type archive by author, add author crumb.
		// This handles URLs like `/{type}?=author={author}`.
		if (is_author()) {
			$user = $this->user ?: new WP_User(get_query_var('author'));

			// Add author crumb.
			$this->context->addCrumb(CrumbRegistrar::AUTHOR, [
				'user' => $user
			]);
		}

		// If viewing a post type search, add the search crumb. This
		// handles URLs like `/?s={search}&post_type={type}`.
		if (is_search()) {
			$this->context->addCrumb(CrumbRegistrar::SEARCH);
		}

		$this->context->assemble(AssemblerRegistrar::PAGED);
	}
}
