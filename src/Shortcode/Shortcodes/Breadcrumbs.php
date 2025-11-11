<?php

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Shortcode\Shortcodes;

use X3P0\Breadcrumbs\Shortcode\Shortcode;

final class Breadcrumbs implements Shortcode
{
	private const BLOCK_NAME = 'x3p0/breadcrumbs';

	private const DEFAULT_ATTRS = [
		'home_icon'        => '',
		'link_last_crumb'  => '',
		'label_error_404'  => '',
		'label_home'       => '',
		'label_search'     => '',
		'justify_content'  => '',
		'map_rewrite_tags' => '',
		'markup_type'      => '',
		'post_taxonomy'    => '',
		'separator_icon'   => '',
		'show_home_label'  => '',
		'show_on_homepage' => '',
		'show_first_crumb' => '',
		'show_last_crumb'  => ''
	];

	private const ATTRS_MAP = [
		'home_icon'        => 'homeIcon',
		'link_last_crumb'  => 'linkTrailEnd',
		'justify_content'  => 'justifyContent',
		'markup_type'      => 'markup',
		'separator_icon'   => 'separatorIcon',
		'show_home_label'  => 'showHomeLabel',
		'show_on_homepage' => 'showOnHomePage',
		'show_first_crumb' => 'showTrailStart',
		'show_last_crumb'  => 'showTrailEnd'
	];

	public function render(
		array   $attrs     = [],
		?string $content   = null,
		string  $shortcode = ''
	): string {
		$attrs = shortcode_atts(self::DEFAULT_ATTRS, $attrs, $shortcode);

		return render_block([
			'blockName' => self::BLOCK_NAME,
			'attrs'     => $this->parseAttributes($attrs)
		]);
	}

	private function parseAttributes(array $attrs): array
	{
		$blockAttrs = [
			'labels' => [],
			'mapRewriteTags' => [],
			'postTaxonomy' => [],
		];

		$strAttrs = [
			'home_icon',
			'justify_content',
			'markup_type',
			'separator_icon'
		];

		foreach ($strAttrs as $strAttr) {
			if ('' !== $attrs[$strAttr]) {
				$blockAttrs[self::ATTRS_MAP[$strAttr]] = $attrs[$strAttr];
			}
		}

		$boolAttrs = [
			'link_last_crumb',
			'show_home_label',
			'show_on_homepage',
			'show_first_crumb',
			'show_last_crumb'
		];

		foreach ($boolAttrs as $boolAttr) {
			if ('' !== $attrs[$boolAttr]) {
				$blockAttrs[self::ATTRS_MAP[$boolAttr]] = $this->parseBoolean($attrs[$boolAttr]);
			}
		}

		if ('' !== $attrs['map_rewrite_tags']) {
			$parts = explode(',', $attrs['map_rewrite_tags']);
			foreach ($parts as $part) {
				$components = explode(':', trim($part));
				if (
					2 === count($components)
					&& post_type_exists($components[0])
				) {
					$blockAttrs['mapRewriteTags'][$components[0]] = $this->parseBoolean($components[1]);
				}
			}
		}


		if ('' !== $attrs['post_taxonomy']) {
			$parts = explode(',', $attrs['post_taxonomy']);
			foreach ($parts as $part) {
				$components = explode(':', trim($part));
				if (
					2 === count($components)
					&& post_type_exists($components[0])
					&& taxonomy_exists($components[1])
				) {
					$blockAttrs['postTaxonomy'][$components[0]] = $components[1];
				}
			}
		}

		$labelAttrs = [
			'label_error_404' => 'error_404',
			'label_home'      => 'home',
			'label_search'    => 'search'
		];

		foreach ($labelAttrs as $labelAttr => $blockKey) {
			if ('' !== $attrs[$labelAttr]) {
				$blockAttrs['labels'][$blockKey] = $attrs[$labelAttr];
			}
		}

		return $blockAttrs;
	}

	private function parseBoolean(mixed $value): bool
	{
		if (is_bool($value)) {
			return $value;
		}

		return filter_var($value, FILTER_VALIDATE_BOOLEAN);
	}
}
