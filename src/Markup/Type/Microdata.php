<?php

/**
 * Microdata markup class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup\Type;

use X3P0\Breadcrumbs\Crumb\Crumb;

/**
 * Extends the plain HTML list with Schema.org microdata (`itemscope`,
 * `itemtype`, `itemprop`) so the visible trail also exposes a `BreadcrumbList`
 * to search engines.
 */
final class Microdata extends Html
{
	/**
	 * @inheritDoc
	 */
	public static function label(): string
	{
		return __('Microdata (Schema.org)', 'x3p0-breadcrumbs');
	}

	/**
	 * @inheritDoc
	 */
	public function render(): string
	{
		if (! $this->isRenderable()) {
			return '';
		}

		return sprintf(
			'<nav %s><ol class="%s" itemscope itemtype="https://schema.org/BreadcrumbList">%s</ol></nav>',
			$this->containerAttr(),
			esc_attr($this->scopeClass('trail')),
			$this->renderCrumbs()
		);
	}

	/**
	 * Renders a crumb as a `ListItem`-typed list item, adding the microdata
	 * properties and a `position` meta tag.
	 */
	protected function renderCrumb(Crumb $crumb): string
	{
		if (! $this->isCrumbRenderable($crumb)) {
			return '';
		}

		return sprintf(
			'<li class="%1s" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"%s>
				%s
				<meta itemprop="position" content="%s"/>
			</li>',
			esc_attr($this->scopeClass([
				'crumb',
				'crumb--' . $this->crumbs->currentType()
			])),
			$this->crumbs->isLast() ? ' aria-current="page"' : '',
			$this->renderCrumbContent($crumb),
			esc_attr((string)$this->crumbs->position())
		);
	}

	/**
	 * Renders the crumb's content with microdata annotations: a `name`-labeled
	 * span, output as an `item` link when linkable or as a `WebPage`-typed span
	 * otherwise.
	 */
	private function renderCrumbContent(Crumb $crumb): string
	{
		// Filter out any unwanted HTML from the label.
		$label = sprintf(
			'<span class="%s" itemprop="name">%s</span>',
			esc_attr($this->scopeClass('crumb-label')),
			wp_kses($crumb->getLabel(), self::ALLOWED_HTML)
		);

		// Return the linked content if the crumb has a URL.
		if ($this->isCrumbLinkable($crumb)) {
			return sprintf(
				'<a href="%s" class="%s" itemprop="item">%s</a>',
				esc_url($crumb->getUrl()),
				esc_attr($this->scopeClass('crumb-content')),
				$label
			);
		}

		// Return an unlinked span if there's no URL.
		return sprintf(
			'<span class="%s" itemscope itemid="%s" itemtype="https://schema.org/WebPage" itemprop="item">%s</span>',
			esc_attr($this->scopeClass('crumb-content')),
			esc_url($crumb->getUrl()),
			$label
		);
	}
}
