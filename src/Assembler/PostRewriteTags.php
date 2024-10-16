<?php

/**
 * Post rewrite tags assembler.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler;

use WP_Post;
use WP_User;
use X3P0\Breadcrumbs\Contracts\Builder;

/**
 * This class accepts a permalink structure and attempts to map any rewrite tags
 * like `%tag%` to a breadcrumb. This is used with any post type.  It maps the
 * core WP `%year%`, `%monthnum%`, `%day%`, and `%author` tags. It will also map
 * any taxonomy tags.
 */
class PostRewriteTags extends Assembler
{
	/**
	 * {@inheritdoc}
	 */
	public function __construct(
		protected Builder $builder,
		protected WP_Post $post,
		protected string $path = ''
	) {
		parent::__construct($this->builder);
	}

	/**
	 * {@inheritdoc}
	 */
	public function assemble(): void
	{
		// Bail early if rewrite tag mapping is disabled.
		if (! $this->builder->mapRewriteTags($this->post->post_type)) {
			return;
		}

		$segments = explode('/', trim($this->path, '/'));

		foreach ($segments as $tag) {
			$this->mapTag($tag);
		}
	}

	/**
	 * Maps a rewrite tag to a crumb or assembler implementation.
	 */
	private function mapTag(string $tag): void
	{
		match ($tag) {
			'%year%' => $this->builder->addCrumb('year', [
				'post' => $this->post
			]),
			'%monthnum%' => $this->builder->addCrumb('month', [
				'post' => $this->post
			]),
			'%day%' => $this->builder->addCrumb('day', [
				'post' => $this->post
			]),
			'%author%' => $this->builder->addCrumb('author', [
				'user' => new WP_User($this->post->post_author)
			]),
			$this->useTaxonomy($tag) => $this->builder->assemble('post-terms', [
				'post'     => $this->post,
				'taxonomy' => get_taxonomy(trim($tag, '%'))
			]),
			default => false
		};
	}

	/**
	 * Helper function to determine whether a rewrite tag is a taxonomy. If
	 * the tag matches a taxonomy name, it returns the original tag. Else,
	 * it returns `null`. The taxonomy will also only match if it was *not*
	 * explicitly added as part of the post crumbs.
	 */
	private function useTaxonomy(string $tag): ?string
	{
		if (! str_starts_with($tag, '%') || ! str_ends_with($tag, '%')) {
			return null;
		}

		$tax = trim($tag, '%');

		return taxonomy_exists($tax) && $tax !== $this->builder->getPostTaxonomy($this->post->post_type)
			? $tag
			: null;
	}
}
