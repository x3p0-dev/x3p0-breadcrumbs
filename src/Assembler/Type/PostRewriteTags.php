<?php

/**
 * Post rewrite tags assembler.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler\Type;

use WP_Post;
use WP_User;
use X3P0\Breadcrumbs\Assembler\Assembler;
use X3P0\Breadcrumbs\Assembler\AssemblerType;
use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Crumb\CrumbType;

/**
 * Maps the rewrite tags in a permalink structure to crumbs for a post. Walks the
 * path one segment at a time, translating the core WP `%year%`, `%monthnum%`,
 * `%day%`, `%hour%`, `%minute%`, `%second%`, and `%author%` tags into date and
 * author crumbs, and any taxonomy tag into a `PostTerms` delegation. Works for
 * any post type and does nothing when rewrite-tag mapping is disabled in config.
 */
final class PostRewriteTags extends Assembler
{
	/**
	 * @inheritDoc
	 */
	public function __construct(
		BreadcrumbsContext $context,
		protected WP_Post $post,
		protected string $path = ''
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function assemble(): void
	{
		// Bail early if rewrite tag mapping is disabled.
		if (! $this->context->config()->mapRewriteTags($this->post->post_type)) {
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
			'%year%' => $this->context->addCrumb(CrumbType::Year, [
				'post' => $this->post
			]),
			'%monthnum%' => $this->context->addCrumb(CrumbType::Month, [
				'post' => $this->post
			]),
			'%day%' => $this->context->addCrumb(CrumbType::Day, [
				'post' => $this->post
			]),
			'%hour%' => $this->context->addCrumb(CrumbType::Hour, [
				'post' => $this->post
			]),
			'%minute%' => $this->context->addCrumb(CrumbType::Minute, [
				'post' => $this->post
			]),
			'%second%' => $this->context->addCrumb(CrumbType::Second, [
				'post' => $this->post
			]),
			'%author%' => $this->context->addCrumb(CrumbType::Author, [
				'user' => new WP_User($this->post->post_author)
			]),
			$this->useTaxonomy($tag) => $this->context->assemble(
				AssemblerType::PostTerms,
				[
					'post'     => $this->post,
					'taxonomy' => get_taxonomy(trim($tag, '%'))
				]
			),
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

		return taxonomy_exists($tax) && $tax !== $this->context->config()->getPostTaxonomy($this->post->post_type)
			? $tag
			: null;
	}
}
