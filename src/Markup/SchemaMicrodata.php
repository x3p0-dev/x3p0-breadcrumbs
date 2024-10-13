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
class SchemaMicrodata extends Html
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
	 * Renders the markup for an individual crumb item.
	 */
	private function renderCrumb(Crumb $crumb, int $count, int $position): string
	{
		// Get the crumb URL and determine whether to link the crumb.
		$url       = $crumb->url();
		$is_last   = $position === $count;
		$show_last = $this->option('show_last_item');
		$has_link  = ($url && ! $is_last) || ($url && $is_last && ! $show_last);

		// Add `.screen-reader-text` class for crumbs with hidden labels.
		// Usually applied to the home crumb when it's replaced with an
		// icon. Filter out any unwanted HTML from the label.
		$label = sprintf(
			'<span class="breadcrumbs__crumb-label%s" itemprop="name">%s</span>',
			$crumb->visuallyHidden() ? ' screen-reader-text' : '',
			wp_kses($crumb->label(), self::ALLOWED_HTML)
		);

		// Wrap the label with a link if the crumb has one and this is
		// not the normal last item. However, link the last item if the
		// original last item was popped off the array.
		$item = sprintf($has_link
			? '<a href="%s" class="breadcrumbs__crumb-content" itemprop="item">%s</a>'
			: '<span class="breadcrumbs__crumb-content" itemscope itemid="%s" itemtype="https://schema.org/WebPage" itemprop="item">%s</span>',
		esc_url($url), $label);

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
