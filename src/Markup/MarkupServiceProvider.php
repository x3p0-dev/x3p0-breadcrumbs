<?php

/**
 * Markup service provider.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup;

use X3P0\Breadcrumbs\Packages\Framework\Core\ServiceProvider;

/**
 * Wires the markup subsystem into the container: binds the factory and options
 * as shared singletons (only if not already bound) and, from the `MarkupType`
 * enum as the source of truth, both aliases each built-in key to its class and
 * tags each class under `Markup::TAG`. The aliases let a format resolve by key
 * or class name like every other subsystem; the tag lets the factory enumerate
 * the full set for the block editor, which stays open to third parties that add
 * their own by aliasing and tagging under the same names.
 */
final class MarkupServiceProvider extends ServiceProvider
{
	/**
	 * The markup factory and options, bound as shared singletons only if not
	 * already bound so extensions may replace them.
	 *
	 * @var  array<int|string, string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const SINGLETONS_IF = [
		MarkupOptions::class,
		MarkupFactory::class
	];

	/**
	 * Aliases each built-in markup key to its class and tags each class under
	 * `Markup::TAG`, both seeded from the `MarkupType` enum as the source of
	 * truth — the alias for key/class resolution, the tag for enumeration.
	 */
	public function register(): void
	{
		foreach (MarkupType::cases() as $type) {
			$this->container->alias($type->alias(), $type->className());
			$this->container->tag($type->className(), Markup::TAG);
		}
	}
}
