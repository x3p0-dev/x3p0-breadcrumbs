<?php

/**
 * Meta service provider.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Meta;

use X3P0\Breadcrumbs\Packages\Framework\Core\ServiceProvider;

/**
 * Wires the Meta subsystem into the container. Boots `MetaRegistrar`, which
 * registers the plugin's post/term meta keys (see the {@see MetaKey} enum)
 * with WordPress on `init`.
 */
final class MetaServiceProvider extends ServiceProvider
{
	/**
	 * Boots `MetaRegistrar` so the plugin's meta keys are registered before
	 * anything needs to read or write them.
	 *
	 * @var  array<int|string, string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const BOOTABLE = [
		MetaRegistrar::class
	];
}
