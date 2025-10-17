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

namespace X3P0\Breadcrumbs\Markup;

use X3P0\Breadcrumbs\Contracts\Crumb;

/**
 * Creates a plain HTML representation of the breadcrumbs as an ordered list.
 */
class Html extends Markup
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
		$html .= '<ol class="breadcrumbs__trail">';

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
			'<li class="breadcrumbs__crumb breadcrumbs__crumb--%s"%s>%s</li>',
			esc_attr($crumb->getType()),
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
			'<span class="breadcrumbs__crumb-label">%s</span>',
			wp_kses($crumb->getLabel(), self::ALLOWED_HTML)
		);

		// Return the linked content if the crumb has a URL.
		if ($this->isCrumbLinkable($crumb)) {
			return sprintf(
				'<a href="%s" class="breadcrumbs__crumb-content">%s</a>',
				esc_url($crumb->getUrl()),
				$label
			);
		}

		// Return an unlinked span if there's no URL.
		return '<span class="breadcrumbs__crumb-content">' . $label . '</span>';
	}
}
