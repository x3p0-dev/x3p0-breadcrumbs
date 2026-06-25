<?php

/**
 * Post type archive query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query\Type;

use WP_Post_Type;
use WP_User;
use X3P0\Breadcrumbs\Assembler\AssemblerType;
use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Crumb\CrumbType;
use X3P0\Breadcrumbs\Query\Query;

/**
 * Builds the trail for a post type archive. Adds the home and post-type steps,
 * then conditionally layers on author, date, and/or search crumbs when the
 * archive is further filtered by those query vars, and finishes with the paged
 * step. This is the convergence point that the author, date, and search queries
 * forward to when a request is also a post type archive.
 */
final class PostTypeArchive extends Query
{
	/**
	 * @inheritDoc
	 *
	 * @param WP_Post_Type $postType Optional post type to build for; falls back
	 *                               to the `post_type` query var when omitted.
	 * @param WP_User      $user     Optional author for an author-filtered
	 *                               archive; falls back to the `author` query var.
	 */
	public function __construct(
		BreadcrumbsContext $context,
		private ?WP_Post_Type $postType = null,
		private ?WP_User $user = null
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function query(): void
	{
		$type = $this->postType ?: get_post_type_object(get_query_var('post_type'));

		$this->context->assemble(AssemblerType::Home);
		$this->context->assemble(AssemblerType::PostType, [
			'postType' => $type,
		]);

		// If viewing a post type archive by author, add author crumb.
		// This handles URLs like `/?post_type={type}&author={author}`.
		if (is_author()) {
			$user = $this->user ?: new WP_User(get_query_var('author'));

			// Add author crumb.
			$this->context->addCrumb(CrumbType::Author, [
				'user' => $user
			]);
		}

		// If viewing a date-filtered post type archive, add date crumbs.
		// This handles URLs like `/?post_type={type}&year={year}`.
		if (is_date()) {
			$this->context->assemble(AssemblerType::Date);
		}

		// If viewing a post type search, add the search crumb. This
		// handles URLs like `/?s={search}&post_type={type}`.
		if (is_search()) {
			$this->context->addCrumb(CrumbType::Search);
		}

		$this->context->assemble(AssemblerType::Paged);
	}
}
