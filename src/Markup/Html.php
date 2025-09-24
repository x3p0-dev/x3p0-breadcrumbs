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
		// Get an array of breadcrumbs or return.
		if (! $crumbs = $this->getCrumbs()) {
			return '';
		}

		// Set baseline count and position variables.
		$count    = count($crumbs);
		$position = 1;

		// Build the breadcrumb trail HTML.
		$html  = "<nav {$this->containerAttr()}>";
		$html .= '<ol class="breadcrumbs__trail">';

		foreach ($crumbs as $crumb) {
			$html .= $this->renderCrumb($crumb, $count, $position);
			++$position;
		}

		$html .= '</ol>';
		$html .= '</nav>';

		// Add before/after wrappers and return.
		return $this->getOption('before') . $html . $this->getOption('after');
	}

	/**
	 * Renders the markup for an individual crumb item.
	 */
	private function renderCrumb(Crumb $crumb, int $count, int $position): string
	{
		// Get the crumb URL and determine whether to link the crumb.
		$url       = $crumb->getUrl();
		$is_last   = $position === $count;
		$show_last = $this->getOption('show_last_item');
		$has_link  = ($url && ! $is_last) || ($url && $is_last && ! $show_last);

		// Filter out any unwanted HTML from the label.
		$label = sprintf(
			'<span class="breadcrumbs__crumb-label">%s</span>',
			wp_kses($crumb->getLabel(), self::ALLOWED_HTML)
		);

		// Wrap the label with a link if the crumb has one and this is
		// not the normal last item. However, link the last item if the
		// original last item was popped off the array.
		$item = sprintf(
			$has_link
				? '<a href="%s" class="breadcrumbs__crumb-content">%s</a>'
				: '<span class="breadcrumbs__crumb-content">%2$s</span>',
			esc_url($url),
			$label
		);

		// Build the list item.
		return sprintf(
			'<li class="breadcrumbs__crumb breadcrumbs__crumb--%s"%s>%s</li>',
			$is_last && $show_last ? ' aria-current="page"' : '',
			esc_attr($crumb->getType()),
			$item
		);
	}
}
