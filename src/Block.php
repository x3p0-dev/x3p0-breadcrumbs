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

use WP_Block_Supports;
use X3P0\Breadcrumbs\Contracts\Bootable;
use X3P0\Breadcrumbs\Builder\Builder;
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
		// The option for showing the home label should only ever be
		// triggered if there's an icon set for it.
		if ($attributes['homePrefix'] && $attributes['homePrefixType']) {
			$show_home = $attributes['showHomeLabel'];
		}

		$builder_options = [
			'labels'	   => [ 'title' => '' ],
			'post_taxonomy'    => [ 'post' => 'category' ],
			'map_rewrite_tags' => [ 'post' => false ],
			'show_home_label'  => $show_home ?? true
		];

		$markup_options = [
			'show_on_front'  => $attributes['showOnHomepage'] ?? false,
			'show_last_item' => $attributes['showTrailEnd']   ?? true,
			'container_attr' => $this->getWrapperAttributes($attributes)
		];

		// Build the breadcrumb trail.
		$environment = new Environment();
		$builder     = new Builder($environment, $builder_options);

		// Get the breadcrumb trail markup.
		$markup = match ($attributes['markup'] ?? 'microdata') {
			'microdata' => new Microdata($builder, $markup_options),
			'rdfa'      => new Rdfa($builder, $markup_options),
			default     => new Html($builder, $markup_options)
		};

		return $markup->render();
	}

	/**
	 * A custom wrapper attributes function for the rendered block is needed
	 * over the WordPress `get_block_wrapper_attributes()` function. This is
	 * because the breadcrumb markup implementations require attributes be
	 * passed as an array.
	 */
	private function getWrapperAttributes(array $attributes): array
	{
		// Set up some default class names.
		$classes = ['breadcrumbs' => 'breadcrumbs'];

		// If there is a selected home prefix, define the class.
		if ($attributes['homePrefix'] && $attributes['homePrefixType']) {
			$classes['home'] = sprintf(
				'has-home-%s-%s',
				$attributes['homePrefixType'],
				$attributes['homePrefix']
			);
		}

		// If there's a selected separator, define the class for it.
		if ($attributes['separator'] && $attributes['separatorType']) {
			$classes['sep'] = sprintf(
				'has-sep-%s-%s',
				$attributes['separatorType'],
				$attributes['separator']
			);
		}

		// If there's a selected content justification, add a class.
		if (! empty($attributes['justifyContent'])) {
			$classes['justify'] = sprintf(
				'is-content-justification-%s',
				$attributes['justifyContent']
			);
		}

		// Get the block attributes from block supports.
		$attr = WP_Block_Supports::get_instance()->apply_block_supports();

		// If there's a class from the block attributes, explode it and
		// append the results to our array of classes.
		if (isset($attr['class'])) {
			$classes = $classes + explode(' ', $attr['class'] );
		}

		// Join all classes into a single string.
		$attr['class'] = implode(' ', $classes);

		return $attr;
	}
}
