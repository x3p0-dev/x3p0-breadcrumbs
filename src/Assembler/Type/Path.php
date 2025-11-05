<?php

/**
 * Path assembler.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler\Type;

use WP_Post;
use X3P0\Breadcrumbs\Assembler\{AbstractAssembler, AssemblerRegistrar};
use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Crumb\CrumbRegistrar;
use X3P0\Breadcrumbs\Tools\Helpers;

/**
 * Assembles breadcrumbs based on a given path by attempting to find a post
 * object within that path.
 */
final class Path extends AbstractAssembler
{
	/**
	 * @inheritDoc
	 */
	public function __construct(
		protected BreadcrumbsContext $context,
		protected string $path = ''
	) {
		parent::__construct(...func_get_args());
	}

	/**
	 * @inheritDoc
	 */
	public function assemble(): void
	{
		if (! $path = trim($this->path, '/')) {
			return;
		}

		// If the path is a post, run the parent crumbs and bail early.
		if ($post = get_page_by_path($path)) {
			$this->context->assemble(AssemblerRegistrar::POST_ANCESTORS, [
				'post' => $post
			]);

			$this->context->addCrumb(CrumbRegistrar::POST, [
				'post' => $post
			]);

			return;
		}

		// Split the $path into segments.
		$segments = explode('/', $path);

		// Reverse the array of matches to search for posts in the
		// proper order and Loop through each of the path matches.
		foreach (array_reverse($segments) as $slug) {
			// Get the parent post by the given path.
			$post = get_page_by_path($slug);

			// If a parent post is found, assemble the crumbs via
			// post ancestor.
			if ($post instanceof WP_Post) {
				$this->context->assemble(AssemblerRegistrar::POST_ANCESTORS, [
					'post' => $post
				]);

				$this->context->addCrumb(CrumbRegistrar::POST, [
					'post' => $post
				]);

				break;

			// If the slug matches a post type, let's assemble that
			// by post type and break out of the loop.
			} elseif ($types = Helpers::getPostTypesBySlug($slug)) {
				$this->context->assemble(AssemblerRegistrar::POST_TYPE, [
					'postType' => $types[0]
				]);

				break;
			}
		}
	}
}
