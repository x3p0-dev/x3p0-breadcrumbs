<?php

/**
 * Breadcrumbs block class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

use X3P0\Breadcrumbs\Contracts\Bootable;
use X3P0\Breadcrumbs\Environment\Environment;
use X3P0\Breadcrumbs\Markup\{Html, Microdata, Rdfa};

/**
 * The block class registers and renders the block type on the front end.
 */
class Block implements Bootable
{
	/**
	 * Sets up object state. Note that the `$path` property should point to
	 * the plugin's root folder.
	 */
	public function __construct(protected string $path)
	{}

	/**
	 * Boots the class, running its actions/filters.
	 */
	public function boot(): void
	{
		add_action('init', [ $this, 'register' ]);
	}

	/**
	 * Registers the block with WordPress.
	 */
	public function register(): void
	{
		register_block_type($this->path . '/public', [
			'render_callback' => [ $this, 'render' ]
		]);
	}

	/**
	 * Renders the block on the front end.
	 */
	public function render(array $attributes): string
	{
		$breadcrumb_options = [
			'labels'	   => [ 'title' => '' ],
			'post_taxonomy'    => [ 'post' => 'category' ],
			'map_rewrite_tags' => [ 'post' => false ],
		];

		$markup_options = [
			'container_tag'  => '',
			'show_on_front'  => $attributes['showOnHomepage'] ?? false,
			'show_last_item' => $attributes['showTrailEnd']   ?? true
		];

		// Set up some default class names.
		$home_class    = '';
		$sep_class     = 'has-sep-mask-chevron';
		$justify_class = '';

		// If there is a selected home prefix, define the class.
		if (
			! empty($attributes['homePrefix'])
			&& ! empty($attributes['homePrefixType'])
		) {
			$home_class = sprintf(
				'has-home-%s-%s',
				$attributes['homePrefixType'],
				$attributes['homePrefix']
			);

			// Set whether the home label should be shown. This is
			// wrapped within the prefix check because the home label
			// should always be shown if there's no prefix/icon.
			$breadcrumb_options['show_home_label'] = $attributes['showHomeLabel'] ?? true;
		}

		// If there's a selected separator, define the class for it.
		if (
			! empty($attributes['separator'])
			&& ! empty($attributes['separatorType'])
		) {
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

		// Build the breadcrumb trail.
		$environment = new Environment();
		$breadcrumbs = new Breadcrumbs($environment, $breadcrumb_options);

		// Get the breadcrumb trail markup.
		$markup = match ($attributes['markup'] ?? 'microdata') {
			'microdata' => new Microdata($breadcrumbs, $markup_options),
			'rdfa'      => new Rdfa($breadcrumbs, $markup_options),
			default     => new Html($breadcrumbs, $markup_options)
		};

		// If there is no trail based on the arguments, bail.
		if (! $trail = $markup->render()) {
			return '';
		}

		// Passes custom attributes to the block wrapper function and
		// gets an escaped and formatted wrapper attribute string.
		$wrapper_attributes = get_block_wrapper_attributes([
			'role'       => 'navigation',
			'aria-label' => __('Breadcrumbs', 'x3p0-breadcrumbs'),
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
