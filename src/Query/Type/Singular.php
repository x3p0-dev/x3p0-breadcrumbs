<?php

/**
 * Singular query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query\Type;

use WP_Exception;
use WP_Post;
use X3P0\Breadcrumbs\Assembler\AssemblerType;
use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Query\Query;

/**
 * Builds the trail for a singular request (single post, page, attachment, or
 * other single object): the home step, the post step (which expands the post's
 * hierarchy and type ancestry), and the paged step.
 */
final class Singular extends Query
{
	/**
	 * @inheritDoc
	 *
	 * @param WP_Post $post Optional post to build for; falls back to the
	 *                      queried object when omitted.
	 */
	public function __construct(
		BreadcrumbsContext $context,
		private readonly ?WP_Post $post = null
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 * @throws WP_Exception
	 */
	public function query(): void
	{
		$post = $this->post ?: $this->queriedObject(WP_Post::class);

		$this->context->assemble(AssemblerType::Home);

		// Skip the post step when the queried object is not a post, so a
		// query left in an unexpected state degrades to a safe trail
		// instead of passing a wrong type into the post assembler.
		if ($post instanceof WP_Post) {
			$this->context->assemble(AssemblerType::Post, [ 'post' => $post ]);
		}

		$this->context->assemble(AssemblerType::Paged);
	}
}
