<?php

/**
 * Abstract markup class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup;

use X3P0\Breadcrumbs\Crumb\Crumb;
use X3P0\Breadcrumbs\Crumb\CrumbCollection;
use X3P0\Breadcrumbs\Packages\Framework\Contracts\Renderable;
use X3P0\Breadcrumbs\Tools\Helpers;

/**
 * Base class for rendering a finished breadcrumb trail to a string in a specific
 * format (e.g., HTML, RDFa, Microdata, JSON-LD). It is constructed with the
 * `CrumbCollection` to render and a `MarkupConfig` describing how to display it,
 * and implements the `Renderable` contract. Concrete formats extend this class
 * and supply the actual `render()` output; the shared helpers here handle the
 * common decisions of what is renderable, what is linkable, and how classes are
 * namespaced.
 */
abstract class Markup implements Renderable
{
	/**
	 * Whitelist of inline HTML elements (and their permitted attributes) that
	 * are allowed within a crumb label. Pass this to `wp_kses()` to strip any
	 * other markup from a label before output.
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
	 * Stores the crumb collection to render and the config that governs how the
	 * trail is displayed.
	 */
	public function __construct(
		protected readonly CrumbCollection $crumbs,
		protected readonly MarkupConfig $config
	) {}

	/**
	 * Flattens the configured container attributes into an escaped, space-
	 * separated `name="value"` string for inclusion in the container tag.
	 */
	protected function containerAttr(): string
	{
		$attrs    = array_keys($this->config->getContainerAttr());
		$values   = array_values($this->config->getContainerAttr());
		$callback = fn($attr, $value) => sprintf('%s="%s"', esc_attr($attr), esc_attr($value));

		return implode(' ', array_map($callback, $attrs, $values));
	}

	/**
	 * Determines whether the trail as a whole should be rendered. Returns
	 * `false` when there are no crumbs, when on a non-paged front page that
	 * is configured to hide breadcrumbs, or when hiding the first/last
	 * crumb would leave nothing visible.
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
			- ($this->config->showFirstCrumb() ? 0 : 1)
			- ($this->config->showLastCrumb() ? 0 : 1);

		return $visible_count > 0;
	}

	/**
	 * Determines whether a given crumb should be rendered, primarily by
	 * ensuring that it has a valid label. It is also hidden when it is the
	 * first or last item and the config is set to suppress that position.
	 */
	protected function isCrumbRenderable(Crumb $crumb): bool
	{
		if (! $crumb->getLabel()) {
			return false;
		}

		return ! (
			($this->crumbs->isFirst() && ! $this->config->showFirstCrumb())
			|| ($this->crumbs->isLast() && ! $this->config->showLastCrumb())
		);
	}

	/**
	 * Determines whether a crumb should be output as a link. It must have a
	 * URL, and the last crumb is only linked when the config opts in via
	 * `linkLastCrumb()`.
	 */
	protected function isCrumbLinkable(Crumb $crumb): bool
	{
		if (! $crumb->getUrl()) {
			return false;
		}

		$is_last = $this->crumbs->count() === $this->crumbs->position();

		return ! $is_last || $this->config->linkLastCrumb();
	}

	/**
	 * Prefixes one or more class names with the configured namespace in BEM
	 * fashion (`{namespace}__{class}`) and returns them as a single space-
	 * separated string.
	 */
	protected function scopeClass(string|array $class): string
	{
		$namespace = $this->config->namespace();

		return implode(' ', array_map(
			fn($className) => "{$namespace}__{$className}",
			(array) $class
		));
	}
}
