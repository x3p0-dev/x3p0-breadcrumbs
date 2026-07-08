<?php

/**
 * Markup registration class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup;

use X3P0\Breadcrumbs\Support\EnumRegistrar;

/**
 * Seeds the registry with the built-in markup types on boot, mapping each
 * `MarkupType` enum case to its concrete class. Pre-existing registrations for
 * a key are left untouched so third-party overrides win.
 */
final class MarkupRegistrar extends EnumRegistrar
{
	/**
	 * The enum whose cases seed the markup registry.
	 *
	 * @var  class-string<MarkupType>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const ENUM = MarkupType::class;

	/**
	 * Type-hints the markup registry so the container injects it, then hands
	 * it to the base registrar.
	 */
	public function __construct(MarkupRegistry $registry)
	{
		parent::__construct($registry);
	}
}
