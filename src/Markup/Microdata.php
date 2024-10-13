<?php

/**
 * Microdata markup class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup;

use X3P0\Breadcrumbs\Contracts\Crumb;

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
		// Get an array of breadcrumbs or return.
		if (! $crumbs = $this->crumbs()) {
			return '';
		}

		// Set baseline count and position variables.
		$count    = count($crumbs);
		$position = 1;

		// Build the breadcrumb trail HTML.
		$html  = sprintf('<nav %s>', $this->containerAttr());
		$html .= '<ol class="breadcrumbs__trail" itemscope itemtype="https://schema.org/BreadcrumbList">';

		foreach ($crumbs as $crumb) {
			$html .= $this->renderCrumb($crumb, $count, $position);
			++$position;
		}

		$html .= '</ol>';
		$html .= '</nav>';

		// Add before/after wrappers and return.
		return $this->option('before') . $html . $this->option('after');
	}

	/**
	 * {@inheritdoc}
	 */
	private function renderCrumb(Crumb $crumb, int $count, int $position): string
	{
		// Add `.screen-reader-text` class for crumbs
		// with hidden labels. Usually applied to the
		// home crumb when it's replaced with an icon.
		$hidden = $crumb->visuallyHidden() ? ' screen-reader-text' : '';

		// Filter out any unwanted HTML from the label.
		$label = sprintf(
			'<span class="%s" itemprop="name">%s</span>',
			esc_attr("breadcrumbs__crumb-label{$hidden}"),
			wp_kses($crumb->label(), self::ALLOWED_HTML)
		);

		// Get the crumb URL.
		$url = $crumb->url();

		// Wrap the label with a link if the crumb has one and this is
		// not the normal last item. However, link the last item if the
		// original last item was popped off the array.
		if (
			($url && $position !== $count)
			|| ($url && $position === $count && ! $this->option('show_last_item'))
		) {
			$item = sprintf(
				'<a href="%s" class="breadcrumbs__crumb-content" itemprop="item">%s</a>',
				esc_url($url),
				$label
			);
		} else {
			$item = sprintf(
				'<span class="breadcrumbs__crumb-content" itemscope itemid="%s" itemtype="https://schema.org/WebPage" itemprop="item">%s</span>',
				esc_url($url),
				$label
			);
		}

		// Build the list item.
		return sprintf(
			'<li class="breadcrumbs__crumb breadcrumbs__crumb--%s" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
				%s
				<meta itemprop="position" content="%s"/>
			</li>',
			esc_attr($crumb->type()),
			$item,
			$position
		);
	}
}
