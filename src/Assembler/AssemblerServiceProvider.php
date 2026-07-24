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

use X3P0\Breadcrumbs\Packages\Framework\Container\ContainerException;
use X3P0\Breadcrumbs\Packages\Framework\Core\ServiceProvider;

/**
 * Wires the assembler subsystem into the container: binds the factory as a
 * shared singletons (only if not already bound) so extensions may replace it,
 * and tags each built-in `AssemblerType` case's class under `Assembler::TAG`.
 * `AssemblerFactory` collects these tagged entries to resolve an assembler by
 * enum case or class name.
 */
final class AssemblerServiceProvider extends ServiceProvider
{
	/**
	 * The assembler factory, bound as a shared singleton only if not
	 * already bound so extensions may replace it.
	 *
	 * @var  array<int|string, string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const SINGLETONS_IF = [
		AssemblerFactory::class
	];

	/**
	 * Tags each built-in assembler to {@see Assembler::TAG}. The enum is
	 * the source of truth for the mapping.
	 *
	 * @throws ContainerException
	 */
	public function register(): void
	{
		$this->container->setTagContract(Assembler::TAG, Assembler::class);

		foreach (AssemblerType::cases() as $type) {
			$this->container->tag($type->className(), Assembler::TAG);
		}
	}
}
