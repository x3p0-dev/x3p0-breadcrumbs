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
use X3P0\Breadcrumbs\Assembler\AssemblerContext;
use X3P0\Breadcrumbs\Assembler\AssemblerType;
use X3P0\Breadcrumbs\Packages\Framework\Container\Attributes\NoAutowire;

/**
 * Picks the first term assigned to the post in the given taxonomy and delegates
 * to the `Term` assembler so that term's full trail is built. Adds nothing when
 * the post has no terms in the taxonomy. When no taxonomy is passed explicitly,
 * resolves it from `BreadcrumbsConfig::getPostTaxonomy()` for the post's type
 * and does nothing if none is configured.
 */
final class PostTerms extends Assembler
{
	/**
	 * @inheritDoc
	 */
	public function __construct(
		AssemblerContext $context,
		#[NoAutowire] private readonly WP_Post $post,
		#[NoAutowire] private readonly ?WP_Taxonomy $taxonomy = null
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function assemble(): void
	{
		if (! $taxonomy = $this->taxonomy ?? $this->resolveTaxonomy()) {
			return;
		}

		// Get the post terms for the given taxonomy.
		$terms = get_the_terms($this->post->ID, $taxonomy->name);

		// Check that terms were returned.
		if ($terms && ! is_wp_error($terms)) {
			$this->context->assemble(AssemblerType::Term, [
				'term' => $terms[0]
			]);
		}
	}

	/**
	 * Resolves the taxonomy configured as representative for the post's type,
	 * or `null` if none is configured or the configured name doesn't resolve.
	 */
	private function resolveTaxonomy(): ?WP_Taxonomy
	{
		$name = $this->context->config->getPostTaxonomy($this->post->post_type);

		return $name ? (get_taxonomy($name) ?: null) : null;
	}
}
