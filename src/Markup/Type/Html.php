<?php

/**
 * HTML markup class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup\Type;

use X3P0\Breadcrumbs\Crumb\Crumb;
use X3P0\Breadcrumbs\Markup\AbstractMarkup;

/**
 * Creates a plain HTML representation of the breadcrumbs as an ordered list.
 */
class Html extends AbstractMarkup
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
			'<nav %s><ol class="%s">%s</ol></nav>',
			$this->containerAttr(),
			esc_attr($this->scopeClasses('trail')),
			$this->renderCrumbs()
		);
	}

	/**
	 * Renders the breadcrumbs items.
	 */
	protected function renderCrumbs(): string
	{
		$html = '';
		$this->crumbs->rewind();

		while ($this->crumbs->valid()) {
			$html .= $this->renderCrumb($this->crumbs->current());
			$this->crumbs->next();
		}

		return $html;
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
			'<li class="%s"%s>%s</li>',
			esc_attr($this->scopeClasses([
				'crumb',
				'crumb--' . $this->crumbs->currentType()
			])),
			$this->crumbs->isLast() ? ' aria-current="page"' : '',
			$this->renderCrumbContent($crumb)
		);
	}

	/**
	 * Renders the markup for an individual crumb's content.
	 */
	private function renderCrumbContent(Crumb $crumb): string
	{
		// Filter out any unwanted HTML from the label.
		$label = sprintf(
			'<span class="%s">%s</span>',
			esc_attr($this->scopeClasses('crumb-label')),
			wp_kses($crumb->getLabel(), self::ALLOWED_HTML)
		);

		// Return the linked content if the crumb has a URL.
		if ($this->isCrumbLinkable($crumb)) {
			return sprintf(
				'<a href="%s" class="%s">%s</a>',
				esc_url($crumb->getUrl()),
				esc_attr($this->scopeClasses('crumb-content')),
				$label
			);
		}

		// Return an unlinked span if there's no URL.
		return sprintf(
			'<span class="%s">%s</span>',
			esc_attr($this->scopeClasses('crumb-content')),
			$label
		);
	}
}
