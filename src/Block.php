<?php

/**
 * Block class registers and renders the block type on the front end.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

namespace X3P0\Breadcrumbs;

use X3P0\Breadcrumbs\Contracts\Bootable;

class Block implements Bootable
{
	/**
	 * Stores the plugin path.
	 *
	 * @since 1.0.0
	 * @todo  Move this to the constructor with PHP 8-only support.
	 */
	protected string $path;

	/**
	 * Sets up object state.
	 *
	 * @since 1.0.0
	 */
	public function __construct(string $path)
	{
		$this->path = $path;
	}

	/**
	 * Boots the class, running its actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function boot(): void
	{
		add_action('init', [ $this, 'register' ]);
	}

	/**
	 * Registers the block with WordPress.
	 *
	 * @since 1.0.0
	 */
	public function register(): void
	{
		register_block_type($this->path . '/public', [
			'render_callback' => [ $this, 'render' ]
		]);
	}

	/**
	 * Renders the block on the front end.
	 *
	 * @since 1.0.0
	 */
	public function render(array $attributes): string
	{
		// Arguments to pass to the `Trail` class.
		$trail_args = [
			'labels'	    => [ 'title' => '' ],
			'container_tag'     => '',
			'post_taxonomy'     => [ 'post' => 'category' ],
			'post_rewrite_tags' => false,
			'show_on_front'     => $attributes['showOnHomepage'] ?? false,
			'show_trail_end'    => $attributes['showTrailEnd']   ?? true
		];

		// Set up some default class names.
		$home_class    = '';
		$sep_class     = 'has-sep-mask-chevron';
		$justify_class = '';

		// If there is a selected home prefix, define the class.
		if (! empty($attributes['homePrefix']) && ! empty($attributes['homePrefixType'])) {
			$home_class = sprintf(
				'has-home-%s-%s',
				$attributes['homePrefixType'],
				$attributes['homePrefix']
			);

			// Set whether the home label should be shown. This is
			// wrapped within the prefix check because the home label
			// should always be shown if there's no prefix/icon.
			$trail_args['show_home_label'] = $attributes['showHomeLabel'] ?? true;
		}

		// If there's a selected separator, define the class for it.
		if (! empty($attributes['separator']) && ! empty($attributes['separatorType'])) {
			$sep_class = sprintf(
				'has-sep-%s-%s',
				$attributes['separatorType'],
				$attributes['separator']
			);
		}

		// If there's a selected content justification, add a class.
		if (! empty($attributes['justifyContent'])) {
			$justify_class = sprintf(
				'is-content-justification-%s',
				$attributes['justifyContent']
			);
		}

		// Get the breadcrumb trail.
		$trail = Trail::render($trail_args);

		// If there is no trail based on the arguments, bail.
		if (! $trail) {
			return '';
		}

		// Passes custom attributes to the block wrapper function and
		// gets an escaped and formatted wrapper attribute string.
		$wrapper_attributes = get_block_wrapper_attributes([
			'role'       => 'navigation',
			'aria-label' => __('Breadcrumbs', 'x3p0-breadcrumbs'),
			'itemprop'   => 'breadcrumb',
			'class'      => "breadcrumbs {$home_class} {$sep_class} {$justify_class}"
		]);

		// And, finally! Returning the breadcrumb trail.
		return sprintf(
			'<nav %1$s>%2$s</nav>',
			$wrapper_attributes,
			$trail
		);
	}
}
