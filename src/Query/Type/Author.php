<?php

/**
 * Author query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query\Type;

use WP_Rewrite;
use WP_User;
use X3P0\Breadcrumbs\Assembler\AssemblerRegistrar;
use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Crumb\CrumbRegistrar;
use X3P0\Breadcrumbs\Query\AbstractQuery;

final class Author extends AbstractQuery
{
	/**
	 * @inheritDoc
	 */
	public function __construct(
		protected BreadcrumbsContext $context,
		protected ?WP_User $user = null
	) {
		parent::__construct(...func_get_args());
	}

	/**
	 * @inheritDoc
	 *
	 * @global WP_Rewrite $GLOBALS['wp_rewrite']
	 */
	public function query(): void
	{
		$user = $this->user ?: new WP_User(get_query_var('author'));

		$this->context->assemble(AssemblerRegistrar::HOME);
		$this->context->assemble(AssemblerRegistrar::REWRITE_FRONT);

		// If author base exists, check for parent pages.
		if ($base = $GLOBALS['wp_rewrite']->author_base) {
			$this->context->assemble(AssemblerRegistrar::PATH, [
				'path' => $base
			]);
		}

		$this->context->addCrumb(CrumbRegistrar::AUTHOR, [ 'user' => $user ]);

		// If viewing an author search, add the search crumb. This
		// handles URLs like `/?s={search}&author_name={name}`.
		if (is_search()) {
			$this->context->addCrumb(CrumbRegistrar::SEARCH);
		}

		$this->context->assemble(AssemblerRegistrar::PAGED);
	}
}
