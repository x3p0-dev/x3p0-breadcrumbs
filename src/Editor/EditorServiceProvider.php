<?php

/**
 * Editor service provider.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Editor;

use X3P0\Breadcrumbs\Packages\Framework\Core\ServiceProvider;

/**
 * Wires the Editor subsystem into the container, which owns the plugin's editor
 * experience outside the block itself. The block's own editor assets stay with
 * the block, since WordPress loads those from its metadata.
 */
final class EditorServiceProvider extends ServiceProvider
{
	/**
	 * Boots `EditorAssets` so the plugin's editor UI is registered alongside
	 * the rest of the editor's.
	 *
	 * @var  array<int|string, string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const BOOTABLE = [
		EditorAssets::class
	];
}
