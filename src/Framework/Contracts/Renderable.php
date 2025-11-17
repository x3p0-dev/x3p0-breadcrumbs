<?php

/**
 * Renderable interface.
 *
 * @version   1.0.0
 * @package   X3P0\Framework
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Framework\Contracts;

/**
 * Defines the contract that renderable classes should utilize. Renderable
 * classes should have a `render()` method with the purpose of rendering escaped
 * and safe HTML output.
 */
interface Renderable
{
	/**
	 * Renders the HTML output for the class.
	 */
	public function render(): string;
}
