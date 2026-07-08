<?php

/**
 * Crumb types registry.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb;

use X3P0\Breadcrumbs\Packages\ClassRegistry\Registry;

/**
 * Stores the `key => class name` mappings for crumb types. New types are added
 * by registering a `Crumb` subclass against a string key, making the crumb
 * subsystem open for extension without touching core files.
 *
 * @extends Registry<Crumb>
 */
final class CrumbRegistry extends Registry
{
	/**
	 * The base type every registered crumb must be a subclass of.
	 *
	 * @var  class-string<Crumb>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const CONTRACT = Crumb::class;
}
