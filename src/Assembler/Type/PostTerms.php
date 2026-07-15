<?php

/**
 * Post terms assembler.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler\Type;

use WP_Post;
use WP_Taxonomy;
use X3P0\Breadcrumbs\Assembler\Assembler;
use X3P0\Breadcrumbs\Assembler\AssemblerType;
use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Packages\Framework\Container\Attributes\NoAutowire;

/**
 * Picks the first term assigned to the post in the given taxonomy and delegates
 * to the `Term` assembler so that term's full trail is built. Adds nothing when
 * the post has no terms in the taxonomy.
 */
final class PostTerms extends Assembler
{
	/**
	 * @inheritDoc
	 */
	public function __construct(
		BreadcrumbsContext $context,
		#[NoAutowire] private readonly WP_Post $post,
		#[NoAutowire] private readonly WP_Taxonomy $taxonomy
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function assemble(): void
	{
		// Get the post terms for the given taxonomy.
		$terms = get_the_terms($this->post->ID, $this->taxonomy->name);

		// Check that terms were returned.
		if ($terms && ! is_wp_error($terms)) {
			$this->context->assemble(AssemblerType::Term, [
				'term' => $terms[0]
			]);
		}
	}
}
