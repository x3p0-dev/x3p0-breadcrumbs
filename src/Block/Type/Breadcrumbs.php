<?php

/**
 * Breadcrumbs block class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Block\Type;

use WP_Block_Supports;
use X3P0\Breadcrumbs\Block\Block;
use X3P0\Breadcrumbs\Builder\TrailBuilder;
use X3P0\Breadcrumbs\Environment\Environment;
use X3P0\Breadcrumbs\Markup\{Html, Microdata, Rdfa};

/**
 * Renders the Breadcrumbs block on the front end.
 */
final class Breadcrumbs implements Block
{
	/**
	 * Sets the block attributes.
	 */
	public function __construct(protected array $attributes)
	{
		$this->mapDeprecatedAttributes();
	}

	/**
	 * {@inheritdoc}
	 */
	public function render(): string
	{
		$builder_options = [
			'labels'           => $this->attributes['labels']         ?? [],
			'map_rewrite_tags' => $this->attributes['mapRewriteTags'] ?? [],
			'post_taxonomy'    => $this->attributes['postTaxonomy']   ?? []
		];

		$markup_options = [
			'container_attr'  => $this->getWrapperAttributes(),
			'link_last_item'  => $this->attributes['linkTrailEnd']   ?? false,
			'show_first_item' => $this->attributes['showTrailStart'] ?? false,
			'show_last_item'  => $this->attributes['showTrailEnd']   ?? true,
			'show_on_front'   => $this->attributes['showOnHomepage'] ?? false
		];

		// Build the breadcrumb trail.
		$environment = new Environment();
		$builder     = new TrailBuilder($environment, $builder_options);

		// Get the breadcrumb trail markup.
		$markup = match ($this->attributes['markup'] ?? 'rdfa') {
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
	private function getWrapperAttributes(): array
	{
		// Set up some default class names.
		$classes = ['breadcrumbs' => 'breadcrumbs'];

		// If there is a selected home prefix, define the class.
		if (
			$this->attributes['showTrailStart']
			&& $this->attributes['homeIcon']
		) {
			$classes['home'] = sprintf(
				'has-home-%s',
				$this->attributes['homeIcon']
			);

			// The option for showing the home label should only ever be
			// triggered if there's an icon set for it.
			if (! $this->attributes['showHomeLabel']) {
				$classes['home-label'] = 'hide-home-label';
			}
		}

		// If there's a selected separator, define the class for it.
		if ($this->attributes['separatorIcon']) {
			$classes['sep'] = sprintf(
				'has-sep-%s',
				$this->attributes['separatorIcon']
			);
		}

		// If there's a selected content justification, add a class.
		if (! empty($this->attributes['justifyContent'])) {
			$classes['justify'] = sprintf(
				'is-content-justification-%s',
				$this->attributes['justifyContent']
			);
		}

		// Get the block attributes from block supports.
		$attr = WP_Block_Supports::get_instance()->apply_block_supports();

		// If there's a class from the block attributes, explode it and
		// append the results to our array of classes.
		if (isset($attr['class'])) {
			$classes = $classes + explode(' ', $attr['class']);
		}

		// Join all classes into a single string.
		$attr['class'] = implode(' ', $classes);

		return $attr;
	}

	/**
	 * Maps deprecated attributes to new attributes.
	 */
	private function mapDeprecatedAttributes(): void
	{
		$separator      = $this->attributes['separator']      ?? null;
		$separatorType  = $this->attributes['separatorType']  ?? null;
		$homePrefix     = $this->attributes['homePrefix']     ?? null;
		$homePrefixType = $this->attributes['homePrefixType'] ?? null;

		if ($separator || $separatorType) {
			$type = 'mask' === $separatorType ? 'svg' : ($separatorType ?: 'svg');
			$icon = $separator ?: 'chevron';
			$this->attributes['separatorIcon'] = "{$type}-{$icon}";
		}

		if ($homePrefix && $homePrefixType) {
			$type = 'mask' === $homePrefixType ? 'svg' : $homePrefixType;
			$this->attributes['homeIcon'] = "{$type}-{$homePrefix}";
		}
	}
}
