<?php
/**
 * Breadcrumbs interface.
 *
 * Defines the interface that breadcrumbs classes must use.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace X3P0\Breadcrumbs\Contracts;

interface Breadcrumbs
{
	/**
	 * Builds a new breadcrumbs object and returns it.
	 *
	 * @since 1.0.0
	 */
	public function make(): self;

	/**
	 * Renders the breadcrumbs HTML output.
	 *
	 * @since 1.0.0
	 */
	public function display(): void;

	/**
	 * Returns the breadcrumbs HTML output.
	 *
	 * @since 1.0.0
	 */
	public function render(): string;

	/**
	 * Returns the breadcrumbs in an array.
	 *
	 * @since 1.0.0
	 */
	public function all(): array;

	/**
	 * Creates a new `\X3P0\Breadcrumbs\Contracts\Query` object and runs
	 * its `make()` method.
	 *
	 * @since 1.0.0
	 */
	public function query( string $type, array $data = [] ): void;

	/**
	 * Creates a new `\X3P0\Breadcrumbs\Contracts\Build` object and runs
	 * its `make()` method.
	 *
	 * @since 1.0.0
	 */
	public function build( string $type, array $data = [] ): void;

	/**
	 * Creates a new `\X3P0\Breadcrumbs\Contracts\Crumb` object and adds
	 * it to the array of crumbs.
	 *
	 * @since 1.0.0
	 */
	public function crumb( string $type, array $data = [] ): void;

	/**
	 * Returns a specific option or `false` if the option doesn't exist.
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public function option( string $name );

	/**
	 * Returns a specific label or an empty string if it doesn't exist.
	 *
	 * @since 1.0.0
	 */
	public function label( string $name ): string;

	/**
	 * Returns a specific post taxonomy or an empty string if one isn't set.
	 *
	 * @since 1.0.0
	 */
	public function postTaxonomy( string $post_type ): string;
}
