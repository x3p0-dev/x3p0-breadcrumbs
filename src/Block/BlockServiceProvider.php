<?php

/**
 * Block service provider.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Block;

use X3P0\Breadcrumbs\Block\Type\Breadcrumbs;
use X3P0\Breadcrumbs\Packages\Framework\Contracts\Bootable;
use X3P0\Breadcrumbs\Packages\Framework\Core\ServiceProvider;

/**
 * Wires up the Block subsystem: binds the Breadcrumbs block type as a
 * singleton and boots the registrar that registers the block with WordPress.
 */
final class BlockServiceProvider extends ServiceProvider implements Bootable
{
	protected const SINGLETONS = [
		Breadcrumbs::class
	];

	protected const BOOTABLE = [
		BlockRegistrar::class,
		BlockAssets::class
	];
}
