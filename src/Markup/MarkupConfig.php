<?php

/**
 * Markup configuration.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup;

/**
 * Creates a config for passing into markup objects, which is used to determine
 * how to display the breadcrumb trail.
 */
final class MarkupConfig
{
	/**
	 * Sets up the initial config state.
	 */
	public function __construct(
		private string $namespace      = 'breadcrumbs',
		private array  $containerAttr  = [],
		private bool   $showOnFront    = false,
		private bool   $showFirstCrumb = true,
		private bool   $showLastCrumb  = true,
		private bool   $linkLastCrumb  = false
	) {
		$this->namespace = sanitize_html_class($this->namespace, 'breadcrumbs');

		$this->containerAttr = array_merge([
			'class'                 => $this->namespace,
			'role'                  => 'navigation',
			'aria-label'            => __('Breadcrumbs', 'x3p0-breadcrumbs'),
			'data-wp-interactive'   => 'x3p0/breadcrumbs',
			'data-wp-router-region' => 'breadcrumbs'
		], $this->containerAttr);
	}

	/**
	 * Static helper function for creating the config from an array.
	 */
	public static function fromArray(array $options): self
	{
		return new self(...array_intersect_key($options, array_flip([
			'namespace',
			'containerAttr',
			'showOnFront',
			'showFirstCrumb',
			'showLastCrumb',
			'linkLastCrumb'
		])));
	}

	/**
	 * Returns the markup namespace, which can be used for class prefixes.
	 */
	public function namespace(): string
	{
		return $this->namespace;
	}

	/**
	 * Gets the container HTML attributes.
	 */
	public function getContainerAttr(): array
	{
		return $this->containerAttr;
	}

	/**
	 * Determines whether to show the markup on the front/homepage.
	 */
	public function showOnFront(): bool
	{
		return $this->showOnFront;
	}

	/**
	 * Determines whether to show the first breadcrumb in the trail.
	 */
	public function showFirstCrumb(): bool
	{
		return $this->showFirstCrumb;
	}

	/**
	 * Determines whether to show the last breadcrumb in the  trail.
	 */
	public function showLastCrumb(): bool
	{
		return $this->showLastCrumb;
	}

	/**
	 * Determines whether to link the last breadcrumb in the trail.
	 */
	public function linkLastCrumb(): bool
	{
		return $this->linkLastCrumb;
	}
}
