<?php

/**
 * Assembler service provider.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler;

use X3P0\Breadcrumbs\Packages\Framework\Core\ServiceProvider;

/**
 * Wires the assembler subsystem into the container: binds the factory as a
 * shared singleton (only if not already bound) so extensions may replace it,
 * and tags each built-in `AssemblerType` case's class under `Assembler::TAG`
 * with its string value as the slug. `AssemblerFactory` collects these tagged
 * entries to resolve an assembler by key, enum case, or class name.
 */
final class AssemblerServiceProvider extends ServiceProvider
{
	/**
	 * The assembler factory, bound as a shared singleton only if not already
	 * bound so extensions may replace it.
	 *
	 * @var  array<int|string, string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const SINGLETONS_IF = [
		AssemblerFactory::class
	];

	/**
	 * Aliases each built-in assembler key to its class, so callers may dispatch
	 * by the `AssemblerType` case, its string key, or the class name. The enum
	 * is the source of truth for the mapping.
	 */
	public function register(): void
	{
		foreach (AssemblerType::cases() as $type) {
			$this->container->tag($type->className(), Assembler::TAG, [
				'slug' => $type->value
			]);
		}
	}
}
