<?php

/**
 * Breadcrumbs interface.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Contracts;

/**
 * Breadcrumbs classes are responsible for creating an array of `Crumb` objects.
 * These can then be used by other classes to output the trail as HTML.
 */
interface Breadcrumbs
{
	/**
	 * Builds a new breadcrumbs object and returns it.
	 */
	public function make(): self;

	/**
	 * Returns the implementation of the breadcrumbs environment in use.
	 */
	public function environment(): Environment;

	/**
	 * Returns the breadcrumbs as an array of `Crumb` objects.
	 *
	 * @return Crumb[]
	 */
	public function all(): array;

	/**
	 * Returns a specific option or `null` if the option doesn't exist.
	 */
	public function option(string $name): mixed;

	/**
	 * Returns a specific label or an empty string if it doesn't exist.
	 */
	public function label(string $name): string;

	/**
	 * Returns a specific post taxonomy or an empty string if one isn't set.
	 */
	public function postTaxonomy(string $post_type): string;

	/**
	 * Creates a new `Query` object and runs it.
	 */
	public function query(string $name, array $data = []): void;

	/**
	 * Creates a new `Builder` object and runs it.
	 */
	public function build(string $name, array $data = []): void;

	/**
	 * Creates a new `Crumb` object and adds it to the crumbs collection.
	 */
	public function crumb(string $name, array $data = []): void;
}
