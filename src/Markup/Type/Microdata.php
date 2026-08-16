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
use X3P0\Breadcrumbs\Markup\MarkupType;

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
	public static function key(): string
	{
		return MarkupType::Microdata->value;
	}

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
	protected function renderTrail(): string
	{
		return sprintf(
			'<ol class="%s" itemscope itemtype="https://schema.org/BreadcrumbList">%s</ol>',
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
			'<li class="%s" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"%s>
				%s
				<meta itemprop="position" content="%s"/>
				%s
			</li>',
			esc_attr($this->scopeClass([
				'crumb',
				'crumb--' . $crumb->getSlug()
			])),
			$this->crumbs->isLast() ? ' aria-current="page"' : '',
			$this->renderCrumbContent($crumb),
			esc_attr((string)$this->crumbs->position()),
			$this->renderSeparator()
		);
	}

	/**
	 * Renders a linkable crumb's content as an `item` link.
	 */
	protected function renderLinkedCrumbContent(Crumb $crumb): string
	{
		return sprintf(
			'<a href="%s" class="%s" itemprop="item">%s%s</a>',
			esc_url($crumb->getUrl()),
			esc_attr($this->scopeClass('crumb-content')),
			$this->renderCrumbIcon($crumb),
			$this->renderCrumbLabel($crumb)
		);
	}

	/**
	 * Renders a non-linkable crumb's content as a `WebPage`-typed `item` span.
	 */
	protected function renderUnlinkedCrumbContent(Crumb $crumb): string
	{
		return sprintf(
			'<span class="%s" itemscope itemid="%s" itemtype="https://schema.org/WebPage" itemprop="item">%s%s</span>',
			esc_attr($this->scopeClass('crumb-content')),
			esc_url($crumb->getUrl()),
			$this->renderCrumbIcon($crumb),
			$this->renderCrumbLabel($crumb)
		);
	}

	/**
	 * Renders a crumb's label as a (kses-filtered) `name`-labeled span.
	 */
	protected function renderCrumbLabel(Crumb $crumb): string
	{
		return sprintf(
			'<span class="%s" itemprop="name">%s</span>',
			esc_attr($this->scopeClass('crumb-label')),
			wp_kses($crumb->getLabel(), self::ALLOWED_HTML)
		);
	}
}
