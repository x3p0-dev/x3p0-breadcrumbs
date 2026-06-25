<?php

/**
 * Author query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query\Type;

use WP_Rewrite;
use WP_User;
use X3P0\Breadcrumbs\Assembler\AssemblerType;
use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Crumb\CrumbType;
use X3P0\Breadcrumbs\Query\Query;
use X3P0\Breadcrumbs\Query\QueryType;

/**
 * Builds the trail for an author archive. Adds the home, rewrite-front, and
 * (when an author base is configured) author-base path steps, then the author
 * crumb, plus a search crumb when the request also carries a search query.
 */
final class Author extends Query
{
	/**
	 * @inheritDoc
	 *
	 * @param WP_User $user Optional author to build for; falls back to the
	 *                      `author` query var when omitted.
	 */
	public function __construct(
		BreadcrumbsContext $context,
		private readonly ?WP_User $user = null
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 *
	 * @global WP_Rewrite $GLOBALS['wp_rewrite']
	 */
	public function query(): void
	{
		// If this is also a post type archive, forward to the post type
		// archive query, which will handle post type + author queries.
		if (is_post_type_archive()) {
			$this->context->query(QueryType::PostTypeArchive);
			return;
		}

		$user = $this->user ?: new WP_User(get_query_var('author'));

		$this->context->assemble(AssemblerType::Home);
		$this->context->assemble(AssemblerType::RewriteFront);

		// If an author base is set in the permalink structure, add path
		// crumbs for it (it may itself resolve to one or more pages).
		if ($base = $GLOBALS['wp_rewrite']->author_base) {
			$this->context->assemble(AssemblerType::Path, [
				'path' => $base
			]);
		}

		$this->context->addCrumb(CrumbType::Author, [ 'user' => $user ]);

		// If viewing an author search, add the search crumb. This
		// handles URLs like `/?s={search}&author_name={name}`.
		if (is_search()) {
			$this->context->addCrumb(CrumbType::Search);
		}

		$this->context->assemble(AssemblerType::Paged);
	}
}
