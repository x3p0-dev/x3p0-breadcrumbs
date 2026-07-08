<?php

/**
 * Crumb registrar class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb;

use X3P0\Breadcrumbs\Support\EnumRegistrar;

/**
 * Seeds the registry with the plugin's built-in crumb types on boot. Each
 * `CrumbType` case maps to its concrete class, and existing registrations are
 * left untouched so third-party overrides take precedence.
 */
final class CrumbRegistrar extends EnumRegistrar
{
	/**
	 * The enum whose cases seed the crumb registry.
	 *
	 * @var  class-string<CrumbType>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const ENUM = CrumbType::class;

	/**
	 * Type-hints the crumb registry so the container injects it, then hands
	 * it to the base registrar.
	 */
	public function __construct(CrumbRegistry $registry)
	{
		parent::__construct($registry);
	}
}
