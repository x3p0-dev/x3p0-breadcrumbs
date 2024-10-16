<?php

/**
 * Path assembler.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler;

use X3P0\Breadcrumbs\Contracts\Builder;
use X3P0\Breadcrumbs\Tools\Helpers;

/**
 * Assembles breadcrumbs based on a given path by attempting to find a post
 * object within that path.
 */
class Path extends Assembler
{
	/**
	 * {@inheritdoc}
	 */
	public function __construct(
		protected Builder $builder,
		protected string $path = ''
	) {
		parent::__construct($this->builder);
	}

	/**
	 * {@inheritdoc}
	 */
	public function assemble(): void
	{
		if (! $path = trim($this->path, '/')) {
			return;
		}

		// If the path is a post, run the parent crumbs and bail early.
		if ($post = get_page_by_path($path)) {
			$this->builder->assemble('post-ancestors', [ 'post' => $post ]);
			$this->builder->addCrumb('post', [ 'post' => $post ]);
			return;
		}

		// Split the $path into an array of strings.
		$matches = explode('/', $path);

		// If matches are found for the path.
		if ([] !== $matches) {
			// Reverse the array of matches to search for posts in
			// the proper order.
			$matches = array_reverse($matches);

			// Loop through each of the path matches.
			foreach ($matches as $slug) {
				// Get the parent post by the given path.
				$post = get_page_by_path($slug);

				// If a parent post is found, Assembler the crumbs
				// and break out of the loop.
				if (! empty($post) && 0 < $post->ID) {
					$this->builder->assemble('post-ancestors', [
						'post' => $post
					]);

					$this->builder->addCrumb('post', [
						'post' => $post
					]);

					break;

				// If the slug matches a post type, let's assemble
				// that and break out of the loop.
				} elseif ($types = Helpers::getPostTypesBySlug($slug)) {
					$this->builder->assemble('post-type', [
						'post_type' => $types[0]
					]);

					break;
				}
			}
		}
	}
}
