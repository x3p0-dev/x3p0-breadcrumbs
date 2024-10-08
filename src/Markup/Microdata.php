<?php

/**
 * Microdata markup class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

namespace X3P0\Breadcrumbs\Markup;

use X3P0\Breadcrumbs\Contracts\Crumb;

class Microdata extends Html
{
	/**
	 * {@inheritdoc}
	 */
	public function render(): string
	{
		$html = $container = $list = $title = '';

		// Get an array of all the available breadcrumbs. Return early
		// if none exist.
		if (! $crumbs = $this->crumbs()) {
			return $html;
		}

		// Set baseline count and position variables.
		$count    = count($crumbs);
		$position = 1;

		// Loop through each of the crumbs and build out the list items.
		foreach ($crumbs as $crumb) {
			$list .= $this->renderCrumb($crumb, $count, $position);
			++$position;
		}

		// Build the list HTML.
		$list = sprintf(
			'<%1$s class="%2$s" itemscope itemtype="https://schema.org/BreadcrumbList">%3$s</%1$s>',
			tag_escape($this->option('list_tag')),
			esc_attr($this->option('list_class')),
			$list
		);

		// Build the title HTML only if there's a label for it.
		if ($this->breadcrumbs->label('title')) {
			$title = sprintf(
				'<%1$s class="%2$s">%3$s</%1$s>',
				tag_escape($this->option('title_tag')),
				esc_attr($this->option('title_class')),
				$this->breadcrumbs->label('title')
			);
		}

		if ($this->option('container_tag')) {
			$container = sprintf(
				'<%1$s class="%2$s" role="navigation" aria-label="%3$s">%4$s</%1$s>',
				tag_escape($this->option('container_tag')),
				esc_attr($this->option('container_class')),
				esc_attr($this->breadcrumbs->label('aria_label')),
				'%1$s%2$s'
			);
		}

		// Build out the final breadcrumbs trail HTML.
		$html = sprintf($container ?: '%1$s%2$s', $title, $list);

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
			esc_attr($this->option('item_label_class') . $hidden),
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
				'<a href="%s" class="%s" itemprop="item">%s</a>',
				esc_url($url),
				esc_attr($this->option('item_content_class')),
				$label
			);
		} else {
			$item = sprintf(
				'<span class="%s" itemscope itemid="%s" itemtype="https://schema.org/WebPage" itemprop="item">%s</span>',
				esc_attr($this->option('item_content_class')),
				esc_url($url),
				$label
			);
		}

		// Get the base class to build modifier classes from.
		$base_class = explode(' ', $this->option('item_class'));
		$base_class = array_shift($base_class);

		$classes = [
			$this->option('item_class'),
			sprintf("{$base_class}--%s", $crumb->type())
		];

		// Build the list item.
		return sprintf(
			'<%1$s class="%2$s" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">%3$s<meta itemprop="position" content="%4$s"/></%1$s>',
			tag_escape($this->option('item_tag')),
			esc_attr(join(' ', $classes)),
			$item,
			$position
		);
	}
}
