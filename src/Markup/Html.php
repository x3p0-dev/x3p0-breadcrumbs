<?php

/**
 * HTML markup class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

namespace X3P0\Breadcrumbs\Markup;

use X3P0\Breadcrumbs\Contracts\{Breadcrumbs, Crumb, Markup};

class Html implements Markup
{
	/**
	 * @var  array
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const ALLOWED_HTML = [
		'abbr'    => [ 'title' => true ],
		'acronym' => [ 'title' => true ],
		'code'    => true,
		'em'      => true,
		'strong'  => true,
		'i'       => true,
		'b'       => true
	];

	/**
	 * Creates a new markup object. The constructor requires a `Breadcrumbs`
	 * implementation and an optional array of arguments for configuring the
	 * generated markup.
	 */
	public function __construct(
		protected Breadcrumbs $breadcrumbs,
		protected array $options
	) {
		$this->options = wp_parse_args($this->options, [
			'show_on_front'      => false,
			'show_trail_end'     => true,
			'before'             => '',
			'after'              => '',
			'container_tag'      => 'nav',
			'title_tag'          => 'h2',
			'list_tag'           => 'ol',
			'item_tag'           => 'li',
			'container_class'    => 'breadcrumbs',
			'title_class'        => 'breadcrumbs__title',
			'list_class'         => 'breadcrumbs__trail',
			'item_class'         => 'breadcrumbs__crumb',
			'item_content_class' => 'breadcrumbs__crumb-content',
			'item_label_class'   => 'breadcrumbs__crumb-label'
		]);

		$this->options = apply_filters(
			'x3p0/breadcrumbs/markup/config',
			$this->options
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function option(string $name): mixed
	{
		return isset($this->options[$name]) ? $this->options[$name] : null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function render(): string
	{
		$html = $container = $list = $title = '';

		// Get an array of all the available breadcrumbs from the
		// builder. Return early if none exist.
		if (! $crumbs = $this->breadcrumbs->all()) {
			return $html;
		}

		$count     = count($crumbs);
		$position  = 1;
		$show_last = $this->option('show_trail_end');

		// Loop through each of the crumbs and build out a list.
		foreach ($crumbs as $crumb) {
			// Break out of the loop if this is the last item
			// and we're not supposed to show the trail end.
			if ($position === $count && ! $show_last) {
				break;
			}

			$list .= $this->renderCrumb($crumb, $count, $position);

			++$position;
		}

		// Build the list HTML.
		$list = sprintf(
			'<%1$s class="%2$s">%3$s</%1$s>',
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
			esc_attr($this->option('item_label_class') . $hidden),
			wp_kses($crumb->label(), self::ALLOWED_HTML)
		);

		// Get the crumb URL.
		$url = $crumb->url();

		// Wrap the label with a link if the crumb has
		// one and this isn't the last item.
		if ($url && $position !== $count) {
			$item = sprintf(
				'<a href="%s" class="%s">%s</a>',
				esc_url($url),
				esc_attr($this->option('item_content_class')),
				$label
			);
		} else {
			$item = sprintf(
				'<span class="%s">%s</span>',
				esc_attr($this->option('item_content_class')),
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
			'<%1$s class="%2$s">%3$s</%1$s>',
			tag_escape($this->option('item_tag')),
			esc_attr(join(' ', $classes)),
			$item
		);
	}
}
