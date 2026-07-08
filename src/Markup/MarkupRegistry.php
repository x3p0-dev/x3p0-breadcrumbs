<?php

/**
 * Markup registry.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup;

use X3P0\Breadcrumbs\Packages\ClassRegistry\Registry;

/**
 * Stores `string key => markup class name` mappings that the factory later
 * resolves and instantiates. Registration is guarded so only `Markup`
 * subclasses can be stored.
 *
 * @extends Registry<Markup>
 */
final class MarkupRegistry extends Registry
{
	/**
	 * The base type every registered markup class must be a subclass of.
	 *
	 * @var  class-string<Markup>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const CONTRACT = Markup::class;
}
