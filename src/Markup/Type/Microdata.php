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

namespace X3P0\Breadcrumbs\Markup\Type;

use X3P0\Breadcrumbs\Crumb\Crumb;

/**
 * Creates an ordered list of the breadcrumbs with Schema.org microdata.
 */
final class Microdata extends Html
{
	/**
	 * @inheritDoc
	 */
	public function render(): string
	{
		if (! $this->isRenderable()) {
			return '';
		}

		return sprintf(
			'<nav %s><ol class="%s__trail" itemscope itemtype="https://schema.org/BreadcrumbList">%s</ol></nav>',
			$this->containerAttr(),
			esc_attr($this->config->namespace()),
			$this->renderCrumbs()
		);
	}

	/**
	 * Renders the markup for an individual crumb item.
	 */
	protected function renderCrumb(Crumb $crumb): string
	{
		if (! $this->isCrumbRenderable($crumb)) {
			return '';
		}

		return sprintf(
			'<li class="%1$s__crumb %1$s__crumb--%2$s" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"%3$s>
				%4$s
				<meta itemprop="position" content="%5$s"/>
			</li>',
			esc_attr($this->config->namespace()),
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
			'<span class="%s__crumb-label" itemprop="name">%s</span>',
			esc_attr($this->config->namespace()),
			wp_kses($crumb->getLabel(), self::ALLOWED_HTML)
		);

		// Return the linked content if the crumb has a URL.
		if ($this->isCrumbLinkable($crumb)) {
			return sprintf(
				'<a href="%s" class="%s__crumb-content" itemprop="item">%s</a>',
				esc_url($crumb->getUrl()),
				esc_attr($this->config->namespace()),
				$label
			);
		}

		// Return an unlinked span if there's no URL.
		return sprintf(
			'<span class="%s__crumb-content" itemscope itemid="%s" itemtype="https://schema.org/WebPage" itemprop="item">%s</span>',
			esc_attr($this->config->namespace()),
			esc_url($crumb->getUrl()),
			$label
		);
	}
}
