<?php

/**
 * Builder interface.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Contracts;

/**
 * Builder classes are responsible for creating an array of `Crumb` objects.
 * These can then be used by other classes to output the trail as HTML.
 */
interface Builder
{
	/**
	 * Builds a new breadcrumbs object and returns it.
	 */
	public function build(): self;

	/**
	 * Returns the breadcrumbs as an array of `Crumb` objects.
	 *
	 * @return Crumb[]
	 */
	public function getCrumbs(): array;

	/**
	 * Returns a specific option or `null` if the option doesn't exist.
	 */
	public function getOption(string $name): mixed;

	/**
	 * Returns a specific label or an empty string if it doesn't exist.
	 */
	public function getLabel(string $name): string;

	/**
	 * Determines whether to map the rewrite tags for a specific post type.
	 */
	public function mapRewriteTags(string $post_type): bool;

	/**
	 * Returns a specific post taxonomy or an empty string if one isn't set.
	 */
	public function getPostTaxonomy(string $post_type): string;

	/**
	 * Creates a new `Query` object and runs it.
	 */
	public function query(string $name, array $params = []): void;

	/**
	 * Creates a new `Assembler` object and runs it.
	 */
	public function assemble(string $name, array $params = []): void;

	/**
	 * Creates a new `Crumb` object and adds it to the crumbs collection.
	 */
	public function addCrumb(string $name, array $params = []): void;
}
