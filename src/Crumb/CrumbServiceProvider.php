<?php

/**
 * Crumb service provider.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb;

use X3P0\Breadcrumbs\Packages\Framework\Container\ContainerException;
use X3P0\Breadcrumbs\Packages\Framework\Core\ServiceProvider;

/**
 * Wires the crumb subsystem into the container: binds the factory as a shared
 * singleton (only if not already bound) so extensions may replace it, and tags
 * each built-in `CrumbType` case's class under `Crumb::TAG`. `CrumbFactory`
 * collects these tagged entries to resolve a crumb by enum case or class name.
 */
final class CrumbServiceProvider extends ServiceProvider
{
	/**
	 * The crumb factory, bound as a shared singleton only if not already bound
	 * so extensions may replace it.
	 *
	 * @var  array<int|string, string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	protected const SINGLETONS_IF = [
		CrumbFactory::class
	];

	/**
	 * Tags each built-in crumb to {@see Crumb::TAG}. The enum is the source
	 * of truth for the mapping.
	 *
	 * @throws ContainerException
	 */
	public function register(): void
	{
		$this->container->setTagContract(Crumb::TAG, Crumb::class);

		foreach (CrumbType::cases() as $type) {
			$this->container->tag($type->className(), Crumb::TAG);
		}
	}
}
