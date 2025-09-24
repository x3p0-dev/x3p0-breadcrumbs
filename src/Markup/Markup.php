<?php

/**
 * Abstract markup class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup;

use X3P0\Breadcrumbs\Contracts;

/**
 * Implements a markup object as an abstract class, providing base properties
 * and methods for subclasses to use to create custom markup implementations.
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
			array_replace_recursive([
				'show_on_front'      => false,
				'show_first_item'    => true,
				'show_last_item'     => true,
				'before'             => '',
				'after'              => '',
				'container_attr'     => [
					'class'      => 'breadcrumbs',
					'role'       => 'navigation',
					'aria-label' => __(
						'Breadcrumbs',
						'x3p0-breadcrumbs'
					)
				]
			], $this->options)
		);
	}

	/**
	 * Helper method for grabbing the breadcrumbs array and removing any
	 * items that should be removed.
	 */
	protected function getCrumbs(): array
	{
		$crumbs = $this->builder->build()->getCrumbs();

		// Remove the first crumb item if it's not supposed to be shown.
		if (! $this->getOption('show_first_item')) {
			array_shift($crumbs);
		}

		// Remove the last crumb item if it's not supposed to be shown.
		if (! $this->getOption('show_last_item')) {
			array_pop($crumbs);
		}

		return $crumbs;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getOption(string $name): mixed
	{
		return $this->options[$name] ?? null;
	}

	/**
	 * Returns a string-based version of the container attributes.
	 */
	protected function containerAttr(): string
	{
		$attrs = '';

		foreach ($this->getOption('container_attr') as $attr => $value) {
			$attrs .= sprintf(' %s="%s"', esc_attr($attr), esc_attr($value));
		}

		// Return the formatted attributes with interactivity API
		// support for client-side navigation by defining a region. We
		// are also adding a reference to a fictional store here to make
		// this work.
		return $attrs . ' data-wp-interactive="x3p0/breadcrumbs" data-wp-router-region';
	}
}
