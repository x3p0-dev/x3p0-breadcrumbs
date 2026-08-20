<?php

/**
 * Markup configuration.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup;

use X3P0\Breadcrumbs\Support\BuildsFromArray;

/**
 * Immutable configuration object passed into markup objects to control how the
 * breadcrumb trail is displayed: the class namespace, container attributes, and
 * the flags governing the first/last crumb and front-page visibility.
 */
final class MarkupConfig
{
	use BuildsFromArray;

	/**
	 * Stores the config values as caller overrides only. The namespace is
	 * sanitized and the container attributes are merged over the defaults
	 * (class, navigation role, ARIA label, and Interactivity API bindings)
	 * lazily in the accessors, so only what the caller passes is stored.
	 *
	 * @param array<string, string> $containerAttr
	 */
	public function __construct(
		private readonly string          $namespace             = 'breadcrumbs',
		private readonly array           $containerAttr         = [],
		private readonly bool            $showOnFront           = false,
		private readonly bool            $showFirstCrumb        = true,
		private readonly bool            $showLastCrumb         = true,
		private readonly bool            $linkLastCrumb         = false,
		private readonly IconVisibility  $iconVisibility        = IconVisibility::None,
		private readonly LabelVisibility $labelVisibility       = LabelVisibility::All,
		private readonly bool            $showSeparator         = true,
		private readonly bool            $showTrailingSeparator = false
	) {}

	/**
	 * Returns the markup namespace, which can be used for class prefixes.
	 */
	public function namespace(): string
	{
		return sanitize_html_class($this->namespace, 'breadcrumbs');
	}

	/**
	 * Gets the container HTML attributes.
	 */
	public function getContainerAttr(): array
	{
		return array_merge([
			'class'                 => $this->namespace(),
			'role'                  => 'navigation',
			'aria-label'            => __('Breadcrumbs', 'x3p0-breadcrumbs'),
			'data-wp-interactive'   => 'x3p0/breadcrumbs',
			'data-wp-router-region' => 'breadcrumbs'
		], $this->containerAttr);
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
	 * Determines whether to show the last breadcrumb in the trail.
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

	/**
	 * Returns which crumbs in the trail should render their icon.
	 */
	public function iconVisibility(): IconVisibility
	{
		return $this->iconVisibility;
	}

	/**
	 * Returns which crumbs in the trail should render their label.
	 */
	public function labelVisibility(): LabelVisibility
	{
		return $this->labelVisibility;
	}

	/**
	 * Determines whether the separator is rendered at all, independent of
	 * which separator icon the `Icon\IconConfig` configures.
	 */
	public function showSeparator(): bool
	{
		return $this->showSeparator;
	}

	/**
	 * Determines whether the separator is also shown after the last crumb,
	 * rather than only between crumbs.
	 */
	public function showTrailingSeparator(): bool
	{
		return $this->showTrailingSeparator;
	}
}
