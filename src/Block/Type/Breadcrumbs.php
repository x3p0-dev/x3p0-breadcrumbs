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

use WP_Block;
use WP_Block_Supports;
use X3P0\Breadcrumbs\Block\Block;
use X3P0\Breadcrumbs\BreadcrumbsService;
use X3P0\Breadcrumbs\Markup\MarkupRegistrar;

/**
 * Renders the Breadcrumbs block on the front end.
 */
final class Breadcrumbs implements Block
{
	/**
	 * Sets the block attributes.
	 */
	public function __construct(protected readonly BreadcrumbsService $breadcrumbsService)
	{}

	/**
	 * @inheritDoc
	 */
	public function render(array $attributes, string $content, WP_Block $block): string
	{
		$attributes = $this->mapDeprecatedAttributes($attributes);

		return $this->breadcrumbsService->render(
			breadcrumbsConfig: [
				'mapRewriteTags' => $attributes['mapRewriteTags'] ?? [],
				'postTaxonomy'   => $attributes['postTaxonomy']   ?? [],
				'labels'         => $attributes['labels']         ?? []
			],
			markupConfig: [
				'namespace'      => 'wp-block-x3p0-breadcrumbs',
				'containerAttr'  => $this->getWrapperAttributes($attributes),
				'showOnFront'    => $attributes['showOnHomepage'] ?? false,
				'showFirstCrumb' => $attributes['showTrailStart'] ?? true,
				'showLastCrumb'  => $attributes['showTrailEnd']   ?? true,
				'linkLastCrumb'  => $attributes['linkTrailEnd']   ?? false
			],
			markupType: $attributes['markup'] ?? MarkupRegistrar::RDFA
		);
	}

	/**
	 * A custom wrapper attributes function for the rendered block is needed
	 * over the WordPress `get_block_wrapper_attributes()` function. This is
	 * because the breadcrumb markup implementations require attributes be
	 * passed as an array.
	 */
	private function getWrapperAttributes(array $attributes): array
	{
		// Get the block attributes from block supports.
		$attr = WP_Block_Supports::get_instance()->apply_block_supports();

		// Define the classes array, pulling from block supports if it
		// has any classes already.
		$classes = isset($attr['class']) ? explode(' ', $attr['class']) : [];

		// If there is a selected home icon, define the class. Also,
		// potentially add the class for hiding the home label, which
		// should only ever be triggered in the case of an icon.
		if ($attributes['showTrailStart'] && $attributes['homeIcon']) {
			$classes[] = sprintf(
				'has-home-%s',
				$attributes['homeIcon']
			);

			if (! $attributes['showHomeLabel']) {
				$classes[] = 'hide-home-label';
			}
		}

		// If there's a selected separator, define the class for it.
		if ($attributes['separatorIcon']) {
			$classes[] = sprintf(
				'has-sep-%s',
				$attributes['separatorIcon']
			);
		}

		// If there's a selected content justification, add a class.
		if (! empty($attributes['justifyContent'])) {
			$classes[] = sprintf(
				'is-content-justification-%s',
				$attributes['justifyContent']
			);
		}

		// Join all classes into a single string and re-add them to the
		// original attributes array.
		$attr['class'] = implode(' ', $classes);

		return $attr;
	}

	/**
	 * Maps deprecated attributes to new attributes.
	 */
	private function mapDeprecatedAttributes(array $attributes): array
	{
		$separator      = $attributes['separator']      ?? null;
		$separatorType  = $attributes['separatorType']  ?? null;
		$homePrefix     = $attributes['homePrefix']     ?? null;
		$homePrefixType = $attributes['homePrefixType'] ?? null;

		if ($separator || $separatorType) {
			$type = 'mask' === $separatorType ? 'svg' : ($separatorType ?: 'svg');
			$icon = $separator ?: 'chevron';
			$attributes['separatorIcon'] = "{$type}-{$icon}";
		}

		if ($homePrefix && $homePrefixType) {
			$type = 'mask' === $homePrefixType ? 'svg' : $homePrefixType;
			$attributes['homeIcon'] = "{$type}-{$homePrefix}";
		}

		return $attributes;
	}
}
