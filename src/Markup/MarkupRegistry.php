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

use X3P0\Breadcrumbs\ClassRegistry;

/**
 * Stores `string key => markup class name` mappings that the factory later
 * resolves and instantiates. Registration is guarded so only `Markup`
 * subclasses can be stored. The inherited `all()` supplies the authoritative
 * list of available markup types, including any registered by third parties.
 *
 * @extends ClassRegistry<Markup>
 */
final class MarkupRegistry extends ClassRegistry
{
	/**
	 * @inheritDoc
	 */
	protected function type(): string
	{
		return Markup::class;
	}
}
