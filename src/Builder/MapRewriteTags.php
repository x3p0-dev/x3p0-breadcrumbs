<?php

/**
 * Map rewrite tags Builder class.
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

namespace X3P0\Breadcrumbs\Builder;

use WP_Post;
use WP_User;
use X3P0\Breadcrumbs\Contracts\Breadcrumbs;

class MapRewriteTags extends Builder
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

		// Split the path into segments and bail early if none found.
		if (! $segments = explode('/', trim($this->path, '/'))) {
			return;
		}

		foreach ($segments as $tag) {
			$this->mapTag(trim($tag, '%'));
		}
	}

	/**
	 * Maps a rewrite tag (with the `%` characters trimmed) to a crumb or
	 * builder implementation.
	 */
	private function mapTag(string $tag): void
	{
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
