<?php

/**
 * Microdata markup class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup;

use X3P0\Breadcrumbs\Crumb\Crumb;

/**
 * Creates an ordered list of the breadcrumbs with Schema.org microdata.
 */
class Microdata extends Html
{
	/**
	 * {@inheritdoc}
	 */
	public function render(): string
	{
		if (! $this->isRenderable()) {
			return '';
		}

		// Build the breadcrumb trail HTML.
		$html  = "<nav {$this->containerAttr()}>";
		$html .= '<ol class="breadcrumbs__trail" itemscope itemtype="https://schema.org/BreadcrumbList">';

		$this->crumbs->rewind();

		while ($this->crumbs->valid()) {
			$html .= $this->renderCrumb($this->crumbs->current());
			$this->crumbs->next();
		}

		$html .= '</ol>';
		$html .= '</nav>';

		// Add before/after wrappers and return.
		return $this->getOption('before') . $html . $this->getOption('after');
	}

	/**
	 * Renders the markup for an individual crumb item.
	 */
	private function renderCrumb(Crumb $crumb): string
	{
		if (! $this->isCrumbRenderable($crumb)) {
			return '';
		}

		return sprintf(
			'<li class="breadcrumbs__crumb breadcrumbs__crumb--%s" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"%s>
				%s
				<meta itemprop="position" content="%s"/>
			</li>',
			esc_attr($this->crumbs->currentType()),
			$this->crumbs->isLast() ? ' aria-current="page"' : '',
			$this->renderCrumbContent($crumb),
			esc_attr($this->crumbs->position())
		);
	}

	/**
	 * Renders the markup for an individual crumb's content.
	 */
	private function renderCrumbContent(Crumb $crumb): string
	{
		// Filter out any unwanted HTML from the label.
		$label = sprintf(
			'<span class="breadcrumbs__crumb-label" itemprop="name">%s</span>',
			wp_kses($crumb->getLabel(), self::ALLOWED_HTML)
		);

		// Return the linked content if the crumb has a URL.
		if ($this->isCrumbLinkable($crumb)) {
			return sprintf(
				'<a href="%s" class="breadcrumbs__crumb-content" itemprop="item">%s</a>',
				esc_url($crumb->getUrl()),
				$label
			);
		}

		// Return an unlinked span if there's no URL.
		return sprintf(
			'<span class="breadcrumbs__crumb-content" itemscope itemid="%s" itemtype="https://schema.org/WebPage" itemprop="item">%s</span>',
			esc_url($crumb->getUrl()),
			$label
		);
	}
}
