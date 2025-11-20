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

use X3P0\Breadcrumbs\Assembler\{AbstractAssembler, AssemblerRegistrar};
use X3P0\Breadcrumbs\BreadcrumbsContext;
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
		BreadcrumbsContext $context,
		protected string $path = ''
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function assemble(): void
	{
		$path = trim($this->path, '/');

		// Iterates through the path, attempting to find an object that
		// uses it for a slug. Each iteration removes a segment from the
		// path before checking again.
		while ($path) {
			if ($this->tryAssemblePath($path)) {
				return;
			}

			$path = $this->removeLastSegment($path);
		}
	}

	/**
	 * Removes the last segment from a path.
	 */
	private function removeLastSegment(string $path): string
	{
		$lastSlash = strrpos($path, '/');

		return $lastSlash !== false ? substr($path, 0, $lastSlash) : '';
	}

	/**
	 * Attempts to assemble breadcrumbs for a given path. Tries both post
	 * and post type assembly.
	 */
	private function tryAssemblePath(string $path): bool
	{
		return $this->tryAssemblePost($path)
			|| $this->tryAssemblePostType($path);
	}

	/**
	 * Attempts to assemble breadcrumbs for a post by path.
	 */
	private function tryAssemblePost(string $path): bool
	{
		if (! $post = get_page_by_path($path)) {
			return false;
		}

		$this->context->assemble(AssemblerRegistrar::POST, [
			'post' => $post
		]);

		return true;
	}

	/**
	 * Attempts to assemble breadcrumbs for a post type by path.
	 */
	private function tryAssemblePostType(string $path): bool
	{
		$types = Helpers::getPostTypesBySlug($path);

		if (empty($types)) {
			return false;
		}

		$this->context->assemble(AssemblerRegistrar::POST_TYPE, [
			'postType' => $types[0]
		]);

		return true;
	}
}
