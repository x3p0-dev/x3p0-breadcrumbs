<?php

/**
 * JSON-LD markup class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup\Type;

use X3P0\Breadcrumbs\Markup\Markup;

/**
 * Renders the trail as a Schema.org `BreadcrumbList` encoded in a JSON-LD
 * `<script>` tag rather than visible HTML, intended for the document head so
 * search engines can read the structured data.
 */
final class JsonLinkedData extends Markup
{
	/**
	 * @inheritDoc
	 */
	public static function type(): string
	{
		return 'json-ld';
	}

	/**
	 * @inheritDoc
	 */
	public function render(): string
	{
		if (! $this->isRenderable()) {
			return '';
		}

		$breadcrumbs = [];

		foreach ($this->crumbs as $crumb) {
			$item = [
				'@type'    => 'ListItem',
				'position' => absint($this->crumbs->position()),
				'name'     => wp_strip_all_tags($crumb->getLabel()),
			];

			if ($this->isCrumbLinkable($crumb)) {
				$item['item'] = esc_url_raw($crumb->getUrl());
			}

			$breadcrumbs[] = $item;
		}

		return sprintf(
			'<script type="application/ld+json">%s</script>',
			wp_json_encode([
				'@context'        => 'https://schema.org',
				'@type'           => 'BreadcrumbList',
				'itemListElement' => $breadcrumbs
			], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
		);
	}
}
