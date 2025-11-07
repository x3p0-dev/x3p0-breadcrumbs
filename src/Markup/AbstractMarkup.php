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

use X3P0\Breadcrumbs\Crumb\{Crumb, CrumbCollection};
use X3P0\Breadcrumbs\Tools\Helpers;

/**
 * Implements a markup object as an abstract class, providing base properties
 * and methods for subclasses to use to create custom markup implementations.
 */
abstract class AbstractMarkup implements Markup
{
	/**
	 * Creates an array of allowed HTML within crumb labels, which should be
	 * used with a function like `wp_kses()`.
	 *
	 * @var  array
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const ALLOWED_HTML = [
		'abbr'    => [ 'class' => true, 'title' => true ],
		'acronym' => [ 'class' => true, 'title' => true ],
		'b'       => [ 'class' => true ],
		'cite'    => [ 'class' => true ],
		'code'    => [ 'class' => true ],
		'del'     => [ 'class' => true ],
		'em'      => [ 'class' => true ],
		'i'       => [ 'class' => true ],
		'ins'     => [ 'class' => true ],
		'mark'    => [ 'class' => true ],
		's'       => [ 'class' => true ],
		'span'    => [ 'class' => true ],
		'strong'  => [ 'class' => true ],
		'sub'     => [ 'class' => true ],
		'sup'     => [ 'class' => true ],
		'u'       => [ 'class' => true ]
	];

	/**
	 * Creates a new markup object. The constructor requires a `Breadcrumbs`
	 * implementation and an optional array of arguments for configuring the
	 * generated markup.
	 */
	public function __construct(
		protected CrumbCollection $crumbs,
		protected MarkupConfig $config
	) {}

	/**
	 * Returns a string-based version of the container attributes.
	 */
	protected function containerAttr(): string
	{
		$attrs    = array_keys($this->config->getContainerAttr());
		$values   = array_values($this->config->getContainerAttr());
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
			&& ! $this->config->showOnFront()
			&& ! Helpers::isPagedView()
		) {
			return false;
		}

		$visible_count = $this->crumbs->count()
			- ($this->config->showFirstItem() ? 0 : 1)
			- ($this->config->showLastItem() ? 0 : 1);

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
			($this->crumbs->isFirst() && ! $this->config->showFirstItem())
			|| ($this->crumbs->isLast() && ! $this->config->showLastItem())
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

		return ! $is_last || $this->config->linkLastItem();
	}

	/**
	 * Helper method for prefixing classes with the namespace.
	 */
	protected function scopeClasses(string|array $class): string
	{
		$namespace = $this->config->namespace();

		return implode(' ', array_map(
			fn($className) => "{$namespace}__{$className}",
			(array) $class
		));
	}
}
