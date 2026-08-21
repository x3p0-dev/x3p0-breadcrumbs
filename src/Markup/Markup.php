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
use X3P0\Breadcrumbs\Icon\IconConfig;
use X3P0\Breadcrumbs\Support\Pagination;

/**
 * Base class for rendering a finished breadcrumb trail to a string in a specific
 * format (e.g., HTML, RDFa, Microdata, JSON-LD). It is constructed with the
 * `CrumbCollection` to render and a `MarkupConfig` describing how to display it,
 * and implements the `Renderable` contract. Concrete formats extend this class
 * and supply the actual `render()` output; the shared helpers here handle the
 * common decisions of what is renderable, what is linkable, and how classes are
 * namespaced.
 */
abstract class Markup
{
	/**
	 * The container tag under which markup types are collected, so the full set
	 * — built-in and third-party — can be resolved by key and enumerated for
	 * the block editor. `MarkupServiceProvider` seeds it from `MarkupType`.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	final public const TAG = 'x3p0/breadcrumbs/markup';

	/**
	 * Whitelist of inline HTML elements (and their permitted attributes) that
	 * are allowed within a crumb label. Pass this to `wp_kses()` to strip any
	 * other markup from a label before output.
	 *
	 * @var  array<string, array<string, bool>>
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
	 * Stores the crumb collection to render, the config that governs how the
	 * trail is displayed, and the caller's icon choices for the markup
	 * layer's own icon option keys (e.g., `separator`).
	 */
	public function __construct(
		protected readonly CrumbCollection $crumbs,
		protected readonly MarkupConfig    $config,
		protected readonly Pagination      $pagination,
		protected readonly IconConfig      $iconConfig = new IconConfig()
	) {}

	/**
	 * Renders the markup and returns its HTML for output on the front end.
	 */
	abstract public function render(): string;

	/**
	 * Flattens the configured container attributes into an escaped, space-
	 * separated `name="value"` string for inclusion in the container tag.
	 */
	protected function containerAttr(): string
	{
		$attr = $this->config->getContainerAttr();

		// Each rendered trail should have a unique router region. This
		// ensures they render correctly when client-side navigation
		// is enabled.
		if (! empty($attr['data-wp-router-region'])) {
			$attr['data-wp-router-region'] = wp_unique_prefixed_id(
				$attr['data-wp-router-region'] . '-'
			);
		}

		return implode(' ', array_map(
			static fn($name, $value) => sprintf('%s="%s"', esc_attr($name), esc_attr($value)),
			array_keys($attr),
			$attr
		));
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
			&& ! $this->pagination->isPagedView()
		) {
			return false;
		}

		$visibleCount = $this->crumbs->count()
			- ($this->config->showFirstCrumb() ? 0 : 1)
			- ($this->config->showLastCrumb() ? 0 : 1);

		return $visibleCount > 0;
	}

	/**
	 * Determines whether a given crumb should be rendered. Position is the
	 * only thing that suppresses one: the first or last crumb, when the
	 * config is set to hide that position. Label-less crumbs never reach
	 * this point — `BreadcrumbsGenerator` drops them from the collection
	 * outright, so every crumb here has something to render.
	 */
	protected function isCrumbRenderable(Crumb $crumb): bool
	{
		return ! (
			($this->crumbs->isFirst() && ! $this->config->showFirstCrumb())
			|| ($this->crumbs->isLast() && ! $this->config->showLastCrumb())
		);
	}

	/**
	 * Determines whether the crumb currently being rendered is the last one
	 * that actually reaches the output. This is not the same question as
	 * `CrumbCollection::isLast()`, which reports a crumb's position in the
	 * accumulated trail and keeps reporting it whether or not the crumb is
	 * rendered: with the last crumb suppressed, the one before it is what
	 * closes the trail visually. Callers that care about how the trail ends
	 * on screen — rather than which crumb is last in the trail itself — want
	 * this one.
	 */
	protected function isLastRenderedCrumb(): bool
	{
		if ($this->crumbs->isLast()) {
			return true;
		}

		// Position is all that can suppress a crumb, and the first is
		// already behind us, so the final crumb is the only one left that
		// might not render — making its predecessor the end of the trail.
		return $this->crumbs->position() === $this->crumbs->count() - 1
			&& ! $this->config->showLastCrumb();
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

		$isLast = $this->crumbs->count() === $this->crumbs->position();

		return ! $isLast || $this->config->linkLastCrumb();
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
			static fn($className) => "{$namespace}__" . str_replace('/', '-', $className),
			(array) $class
		));
	}
}
