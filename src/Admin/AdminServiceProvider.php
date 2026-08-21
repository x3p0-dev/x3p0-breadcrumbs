<?php

/**
 * Admin service provider.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Admin;

use X3P0\Breadcrumbs\Editor\EditorServiceProvider;
use X3P0\Breadcrumbs\Packages\Framework\Core\ServiceProvider;

/**
 * Wires the Admin subsystem into the container, which owns the plugin's UI on
 * WordPress's classic admin screens. What belongs here is what the block editor
 * has no seam for: {@see EditorServiceProvider} covers everything rendered
 * inside an editor, and terms are still edited on `edit-tags.php` and
 * `term.php`, which are ordinary admin pages with an ordinary form post.
 */
final class AdminServiceProvider extends ServiceProvider
{
	/**
	 * Boots `TermIconField`, which decides on its own whether the screen it
	 * landed on is one of the taxonomy screens. `TermIconAssets` is left to the
	 * container to autowire, since the field is the only thing that asks for
	 * it and only once it has decided to render.
	 *
	 * @var  array<int|string, string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const BOOTABLE = [
		TermIconField::class
	];
}
