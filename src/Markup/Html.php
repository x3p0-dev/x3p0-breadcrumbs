<?php

/**
 * HTML markup class.
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
 * Creates a plain HTML representation of the breadcrumbs as an ordered list.
 */
class Html extends Markup
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
		$html .= '<ol class="breadcrumbs__trail">';

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
	 * Renders the markup for an individual crumb item.
	 */
	private function renderCrumb(Crumb $crumb, int $count, int $position): string
	{
		// Add `.screen-reader-text` class for crumbs
		// with hidden labels. Usually applied to the
		// home crumb when it's replaced with an icon.
		$hidden = $crumb->visuallyHidden() ? ' screen-reader-text' : '';

		// Filter out any unwanted HTML from the label.
		$label = sprintf(
			'<span class="%s">%s</span>',
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
				'<a href="%s" class="breadcrumbs__crumb-content">%s</a>',
				esc_url($url),
				$label
			);
		} else {
			$item = sprintf(
				'<span class="breadcrumbs__crumb-content">%s</span>',
				$label
			);
		}

		// Build the list item.
		return sprintf(
			'<li class="breadcrumbs__crumb breadcrumbs__crumb--%s">%s</li>',
			esc_attr($crumb->type()),
			$item
		);
	}
}
