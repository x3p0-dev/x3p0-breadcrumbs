<?php

/**
 * Abstract markup class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup;

use X3P0\Breadcrumbs\Contracts;

/**
 * Implements a markup object as an abstract class, providing base properties
 * and methods for sub-classes to use to create custom markup implementations.
 */
abstract class Markup implements Contracts\Markup
{
	/**
	 * Creates an array of allowed HTML within crumb labels, which should be
	 * used with a function like `wp_kses()`.
	 *
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
		protected Contracts\Builder $builder,
		protected array $options = []
	) {
		$this->options = apply_filters(
			'x3p0/breadcrumbs/markup/config',
			wp_parse_args($this->options, [
				'show_on_front'      => false,
				'show_first_item'    => true,
				'show_last_item'     => true,
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
			])
		);
	}

	/**
	 * Helper method for grabbing the breadcrumbs array and removing any
	 * items that should be removed.
	 */
	protected function crumbs(): array
	{
		$crumbs = $this->builder->getCrumbs();

		// Remove the first crumb item if it's not supposed to be shown.
		if (! $this->option('show_first_item')) {
			array_shift($crumbs);
		}

		// Remove the last crumb item if it's not supposed to be shown.
		if (! $this->option('show_last_item')) {
			array_pop($crumbs);
		}

		return $crumbs;
	}

	/**
	 * {@inheritdoc}
	 */
	public function option(string $name): mixed
	{
		return isset($this->options[$name]) ? $this->options[$name] : null;
	}
}
