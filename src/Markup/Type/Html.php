<?php

/**
 * HTML markup class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup\Type;

use X3P0\Breadcrumbs\Crumb\Crumb;
use X3P0\Breadcrumbs\Markup\Markup;
use X3P0\Breadcrumbs\Markup\MarkupOption;

/**
 * Renders the trail as a plain, semantic ordered list wrapped in a `<nav>`
 * element, with no structured-data vocabulary. Serves as the base for the
 * Microdata and RDFa formats, which extend it to add their own annotations.
 */
class Html extends Markup implements MarkupOption
{
	/**
	 * @inheritDoc
	 */
	public static function label(): string
	{
		return __('Plain HTML', 'x3p0-breadcrumbs');
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
			'<nav %s><ol class="%s">%s</ol></nav>',
			$this->containerAttr(),
			esc_attr($this->scopeClass('trail')),
			$this->renderCrumbs()
		);
	}

	/**
	 * Iterates the collection and concatenates the markup for each crumb into
	 * the list's inner HTML.
	 */
	protected function renderCrumbs(): string
	{
		$html = '';

		foreach ($this->crumbs as $crumb) {
			$html .= $this->renderCrumb($crumb);
		}

		return $html;
	}

	/**
	 * Renders a single crumb as a list item, marking the last one with
	 * `aria-current="page"`. Returns an empty string for crumbs that should not
	 * be rendered.
	 */
	protected function renderCrumb(Crumb $crumb): string
	{
		if (! $this->isCrumbRenderable($crumb)) {
			return '';
		}

		return sprintf(
			'<li class="%s"%s>%s</li>',
			esc_attr($this->scopeClass([
				'crumb',
				'crumb--' . $crumb->getType()
			])),
			$this->crumbs->isLast() ? ' aria-current="page"' : '',
			$this->renderCrumbContent($crumb)
		);
	}

	/**
	 * Renders the inner content of a crumb: the (kses-filtered) label wrapped in
	 * a span, output as a link when the crumb is linkable and as a plain span
	 * otherwise.
	 */
	private function renderCrumbContent(Crumb $crumb): string
	{
		// Filter out any unwanted HTML from the label.
		$label = sprintf(
			'<span class="%s">%s</span>',
			esc_attr($this->scopeClass('crumb-label')),
			wp_kses($crumb->getLabel(), self::ALLOWED_HTML)
		);

		// Return the linked content if the crumb has a URL.
		if ($this->isCrumbLinkable($crumb)) {
			return sprintf(
				'<a href="%s" class="%s">%s</a>',
				esc_url($crumb->getUrl()),
				esc_attr($this->scopeClass('crumb-content')),
				$label
			);
		}

		// Return an unlinked span if there's no URL.
		return sprintf(
			'<span class="%s">%s</span>',
			esc_attr($this->scopeClass('crumb-content')),
			$label
		);
	}
}
