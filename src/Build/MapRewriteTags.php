<?php

/**
 * Map rewrite tags build class.
 *
 * This class accepts a permalink structure and attempts to map any rewrite tags
 * like `%tag%` to a breadcrumb. This is used with any post type.  It maps the
 * core WP `%year%`, `%monthnum%`, `%day%`, and `%author` tags. It will also map
 * any taxonomy tags.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Build;

use WP_Post;
use WP_User;
use X3P0\Breadcrumbs\Contracts\Breadcrumbs;

class MapRewriteTags extends Build
{
	/**
	 * {@inheritdoc}
	 */
	public function __construct(
		protected Breadcrumbs $breadcrumbs,
		protected WP_Post $post,
		protected string $path = ''
	) {}

	/**
	 * {@inheritdoc}
	 */
	public function make(): void
	{
		// Bail early if rewrite tag mapping is disabled.
		if (
			'post' === $this->post->post_type
			&& ! $this->breadcrumbs->option('post_rewrite_tags')
		) {
			return;
		}

		// Trim '/' from both sides of `$this->path` and split into an
		// array of strings.
		$matches = explode('/', trim($this->path, '/'));

		// Bail if no matches are found.
		if (! $matches) {
			return;
		}

		// Loop through each of the matches, adding each to the $trail array.
		foreach ($matches as $tag) {
			$tag = trim($tag, '%');

			match ($tag) {
				'year' => $this->breadcrumbs->crumb('year', [
					'post' => $this->post
				]),
				'monthnum' => $this->breadcrumbs->crumb('month', [
					'post' => $this->post
				]),
				'day' => $this->breadcrumbs->crumb('day', [
					'post' => $this->post
				]),
				'author' => $this->breadcrumbs->crumb('author', [
					'user' => new WP_User($this->post->post_author)
				]),
				$this->isTaxonomy($tag) => $this->breadcrumbs->build('post-terms', [
					'post'     => $this->post,
					'taxonomy' => get_taxonomy($tag)
				]),
				default => false
			};
		}
	}

	/**
	 * Helper function to determine whether a rewrite tag (with the `%`
	 * characters trimmed) is a taxonomy and not already being used as part
	 * of the breadcrumb trail.
	 */
	private function isTaxonomy(string $tag): bool
	{
		return taxonomy_exists($tag)
			&& $tag !== $this->breadcrumbs->postTaxonomy($this->post->post_type);
	}
}
