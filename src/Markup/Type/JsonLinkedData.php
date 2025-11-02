<?php

/**
 * JSON-LD markup class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup\Type;

use X3P0\Breadcrumbs\Markup\AbstractMarkup;

/**
 * Creates a JSON-LD script tag for output in the document head.
 */
class JsonLinkedData extends AbstractMarkup
{
	/**
	 * @inheritDoc
	 */
	public function render(): string
	{
		if (! $this->isRenderable()) {
			return '';
		}

		$breadcrumbs = [];

		$this->crumbs->rewind();

		while ($this->crumbs->valid()) {
			$crumb = [
				'@type'    => 'ListItem',
				'position' => absint($this->crumbs->position()),
				'name'     => wp_strip_all_tags($this->crumbs->current()->getLabel()),
			];

			if ($this->isCrumbLinkable($this->crumbs->current())) {
				$crumb['item'] = esc_url_raw($this->crumbs->current()->getUrl());
			}

			$breadcrumbs[] = $crumb;

			$this->crumbs->next();
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
