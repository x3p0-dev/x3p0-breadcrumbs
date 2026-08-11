<?php

/**
 * User terms assembler.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler\Type;

use WP_Taxonomy;
use WP_User;
use X3P0\Breadcrumbs\Assembler\Assembler;
use X3P0\Breadcrumbs\Assembler\AssemblerContext;
use X3P0\Breadcrumbs\Assembler\AssemblerType;
use X3P0\Breadcrumbs\Crumb\CrumbType;
use X3P0\Breadcrumbs\Packages\Framework\Container\Attributes\NoAutowire;

/**
 * Picks the first term assigned to the user in the given taxonomy and adds its
 * ancestry (via `TermAncestors`) and its own crumb. Deliberately skips `Term`'s
 * own anchor step (owning post type archive, rewrite front, rewrite path) — a
 * caller assembling this into an existing trail (e.g. an author archive)
 * already has its own spine by the time this runs, and reusing `Term`'s anchor
 * step here would build a second, competing one. Adds nothing when the user
 * has no terms in the taxonomy. Not assembled by any built-in query;
 * extensions opt in by calling it directly with the taxonomy to represent.
 */
final class UserTerms extends Assembler
{
	/**
	 * @inheritDoc
	 */
	public function __construct(
		AssemblerContext $context,
		#[NoAutowire] private readonly WP_User $user,
		#[NoAutowire] private readonly WP_Taxonomy $taxonomy
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function assemble(): void
	{
		// Get the user's terms for the given taxonomy. Users have no post
		// cache, so this goes through `wp_get_object_terms()` rather than
		// `get_the_terms()`.
		$terms = wp_get_object_terms($this->user->ID, $this->taxonomy->name);

		// Bail if no terms were returned.
		if (! $terms || is_wp_error($terms)) {
			return;
		}

		$term = $terms[0];

		// Add the term's own ancestry, if it has any.
		if (0 < $term->parent) {
			$this->context->assemble(AssemblerType::TermAncestors, [
				'term' => $term
			]);
		}

		// Add the term crumb.
		$this->context->addCrumb(CrumbType::Term, [
			'term' => $term
		]);
	}
}
