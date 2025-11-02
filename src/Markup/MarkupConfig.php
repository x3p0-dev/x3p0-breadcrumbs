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
		private array $containerAttr = [],
		private bool  $showOnFront   = false,
		private bool  $showFirstItem = true,
		private bool  $showLastItem  = true,
		private bool  $linkLastItem  = false
	) {
		$this->containerAttr = array_merge([
			'class'                 => 'breadcrumbs',
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
		return new self(
			containerAttr: $options['containerAttr'] ?? [],
			showOnFront:   $options['showOnFront']   ?? false,
			showFirstItem: $options['showFirstItem'] ?? true,
			showLastItem:  $options['showLastItem']  ?? true,
			linkLastItem:  $options['linkLastItem']  ?? false
		);
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
	public function showFirstItem(): bool
	{
		return $this->showFirstItem;
	}

	/**
	 * Determines whether to show the last breadcrumb in the  trail.
	 */
	public function showLastItem(): bool
	{
		return $this->showLastItem;
	}

	/**
	 * Determines whether to link the last breadcrumb in the trail.
	 */
	public function linkLastItem(): bool
	{
		return $this->linkLastItem;
	}
}
