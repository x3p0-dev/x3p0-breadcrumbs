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

use X3P0\Breadcrumbs\Block\Renderer\Breadcrumbs;
use X3P0\Breadcrumbs\Packages\Framework\Core\ServiceProvider;

/**
 * Wires up the Block subsystem: binds the Breadcrumbs block renderer as a
 * singleton and boots the registrar, editor asset injector, and inserter
 * filter that register the block with WordPress and prepare its editor
 * experience.
 */
final class BlockServiceProvider extends ServiceProvider
{
	/**
	 * The Breadcrumbs block renderer, bound as a shared singleton.
	 *
	 * @var  array<int|string, string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const SINGLETONS = [
		Breadcrumbs::class
	];

	/**
	 * Services booted on startup: the block registrar and editor assets.
	 *
	 * @var  array<string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const BOOTABLE = [
		BlockRegistrar::class,
		BlockAssets::class,
		BlockInserterFilter::class
	];
}
