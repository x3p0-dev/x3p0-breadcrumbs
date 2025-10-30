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
use X3P0\Breadcrumbs\Crumb\Crumb;
use X3P0\Breadcrumbs\Tools\Helpers;

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
	 * Stores the crumb objects from the builder.
	 */
	protected Contracts\CrumbCollection $crumbs;

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
				'link_last_item'     => false,
				'before'             => '',
				'after'              => '',
				'container_attr'     => [
					'class'      => 'breadcrumbs',
					'role'       => 'navigation',
					'aria-label' => __(
						'Breadcrumbs',
						'x3p0-breadcrumbs'
					),
					'data-wp-interactive'   => 'x3p0/breadcrumbs',
					'data-wp-router-region' => 'breadcrumbs'
				]
			], $this->options)
		);

		$this->crumbs = $this->builder->build()->crumbs();
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
		$attrs    = array_keys($this->getOption('container_attr'));
		$values   = array_values($this->getOption('container_attr'));
		$callback = fn($attr, $value) => sprintf('%s="%s"', esc_attr($attr), esc_attr($value));

		return implode(' ', array_map($callback, $attrs, $values));
	}

	/**
	 * Determines whether the markup is renderable by checking whether there
	 * are crumbs or conditions in which the HTML shouldn't show.
	 */
	protected function isRenderable(): bool
	{
		if ($this->crumbs->isEmpty()) {
			return false;
		}

		if (
			is_front_page()
			&& ! $this->getOption('show_on_front')
			&& ! Helpers::isPagedView()
		) {
			return false;
		}

		$visible_count = $this->crumbs->count()
			- ($this->getOption('show_first_item') ? 0 : 1)
			- ($this->getOption('show_last_item') ? 0 : 1);

		return $visible_count > 0;
	}

	/**
	 * Determines whether a given crumb is renderable primarily be ensuring
	 * that it has a valid label. Otherwise, we determine whether the crumb
	 * is the first or last item and whether they should be displayed based
	 * on options passed into the class.
	 */
	protected function isCrumbRenderable(Crumb $crumb): bool
	{
		if (! $crumb->getLabel()) {
			return false;
		}

		return ! (
			($this->crumbs->isFirst() && ! $this->getOption('show_first_item'))
			|| ($this->crumbs->isLast() && ! $this->getOption('show_last_item'))
		);
	}

	/**
	 * Helper function for determining whether the breadcrumb has a URL and
	 * whether it should be linked based on options passed into the class.
	 */
	protected function isCrumbLinkable(Crumb $crumb): bool
	{
		if (! $crumb->getUrl()) {
			return false;
		}

		$is_last = $this->crumbs->count() === $this->crumbs->position();

		return ! $is_last || $this->getOption('link_last_item');
	}
}
