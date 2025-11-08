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
	public function __construct(
		protected readonly BreadcrumbsService $breadcrumbsService,
		protected array $attributes
	) {
		$this->mapDeprecatedAttributes();
	}

	/**
	 * @inheritDoc
	 */
	public function render(): string
	{
		return $this->breadcrumbsService->render(
			breadcrumbsConfig: [
				'mapRewriteTags' => $this->attributes['mapRewriteTags'] ?? [],
				'postTaxonomy'   => $this->attributes['postTaxonomy']   ?? [],
				'labels'         => $this->attributes['labels']         ?? []
			],
			markupConfig: [
				'namespace'      => 'wp-block-x3p0-breadcrumbs',
				'containerAttr'  => $this->getWrapperAttributes(),
				'showOnFront'    => $this->attributes['showOnHomepage'] ?? false,
				'showFirstCrumb' => $this->attributes['showTrailStart'] ?? true,
				'showLastCrumb'  => $this->attributes['showTrailEnd']   ?? true,
				'linkLastCrumb'  => $this->attributes['linkTrailEnd']   ?? false
			],
			markupType: $this->attributes['markup'] ?? MarkupRegistrar::RDFA
		);
	}

	/**
	 * A custom wrapper attributes function for the rendered block is needed
	 * over the WordPress `get_block_wrapper_attributes()` function. This is
	 * because the breadcrumb markup implementations require attributes be
	 * passed as an array.
	 */
	private function getWrapperAttributes(): array
	{
		// Get the block attributes from block supports.
		$attr = WP_Block_Supports::get_instance()->apply_block_supports();

		// Define the classes array, pulling from block supports if it
		// has any classes already.
		$classes = isset($attr['class']) ? explode(' ', $attr['class']) : [];

		// If there is a selected home icon, define the class. Also,
		// potentially add the class for hiding the home label, which
		// should only ever be triggered in the case of an icon.
		if ($this->attributes['showTrailStart'] && $this->attributes['homeIcon']) {
			$classes[] = sprintf(
				'has-home-%s',
				$this->attributes['homeIcon']
			);

			if (! $this->attributes['showHomeLabel']) {
				$classes[] = 'hide-home-label';
			}
		}

		// If there's a selected separator, define the class for it.
		if ($this->attributes['separatorIcon']) {
			$classes[] = sprintf(
				'has-sep-%s',
				$this->attributes['separatorIcon']
			);
		}

		// If there's a selected content justification, add a class.
		if (! empty($this->attributes['justifyContent'])) {
			$classes[] = sprintf(
				'is-content-justification-%s',
				$this->attributes['justifyContent']
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
