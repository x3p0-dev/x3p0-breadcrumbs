<?php

/**
 * Breadcrumbs block class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Block\Renderer;

use WP_Block;
use WP_Block_Supports;
use X3P0\Breadcrumbs\Block\BlockRenderer;
use X3P0\Breadcrumbs\BreadcrumbsRenderer;
use X3P0\Breadcrumbs\Markup\MarkupOptions;

/**
 * Server-renders the Breadcrumbs block. Translates the block's saved
 * attributes (remapping any deprecated ones) into breadcrumb and markup
 * configuration, then delegates building the trail markup to the injected
 * breadcrumbs renderer. Icon attribute values are passed through as-is; the
 * `Markup` layer resolves them to real markup at render time.
 */
final class Breadcrumbs implements BlockRenderer
{
	/**
	 * Injects the renderer used to build the breadcrumb trail markup.
	 */
	public function __construct(
		private readonly BreadcrumbsRenderer $breadcrumbsRenderer,
		private readonly MarkupOptions       $markupOptions
	) {}

	/**
	 * @inheritDoc
	 */
	public function render(array $attributes, string $content, WP_Block $block): string
	{
		$attributes = $this->mapDeprecatedAttributes($attributes);

		return $this->breadcrumbsRenderer->render(
			breadcrumbsConfig: [
				'labels'         => $attributes['labels']         ?? [],
				'mapRewriteTags' => $attributes['mapRewriteTags'] ?? [],
				'postTaxonomy'   => $attributes['postTaxonomy']   ?? []
			],
			markupConfig: [
				'namespace'             => 'wp-block-x3p0-breadcrumbs',
				'containerAttr'         => $this->getWrapperAttributes($attributes),
				'showOnFront'           => $attributes['showOnHomepage']        ?? false,
				'showFirstCrumb'        => $attributes['showTrailStart']        ?? true,
				'showLastCrumb'         => $attributes['showTrailEnd']          ?? true,
				'linkLastCrumb'         => $attributes['linkTrailEnd']          ?? false,
				'firstCrumbIcon'        => $attributes['homeIcon']              ?? '',
				'separatorIcon'         => $attributes['separatorIcon']         ?? '',
				'showTrailingSeparator' => $attributes['showTrailingSeparator'] ?? false
			],
			markupType: $attributes['markup'] ?? $this->markupOptions->getBlockDefaultKey()
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

		// Hide the home label when there's a home icon rendered in its
		// place. The icon itself is real markup rendered by the `Markup`
		// layer, not driven by a class here.
		if ($attributes['showTrailStart'] && $attributes['homeIcon'] && ! $attributes['showHomeLabel']) {
			$classes[] = 'hide-home-label';
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

		// Add the separator color CSS custom property if defined.
		if ($separatorColor = $this->getSeparatorColor($attributes)) {
			$attr['style'] = ($attr['style'] ?? '') . sprintf(
				'--x3p0-breadcrumbs--color--separator: %s;',
				$separatorColor
			);
		}

		return $attr;
	}

	/**
	 * Returns the separator color value for use in a CSS custom property,
	 * or an empty string if no color is defined.
	 */
	private function getSeparatorColor(array $attributes): string
	{
		if (! empty($attributes['separatorColor'])) {
			return sprintf(
				'var(--wp--preset--color--%s)',
				$attributes['separatorColor']
			);
		}

		return $attributes['customSeparatorColor'] ?? '';
	}

	/**
	 * Maps deprecated attributes to new attributes. Attribute names only —
	 * a deprecated `homeIcon`/`separatorIcon` value produced here (e.g.,
	 * `svg-arrow`) is remapped to its current icon library reference later,
	 * by `Icon\IconResolver`, when the `Markup` layer resolves it.
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
