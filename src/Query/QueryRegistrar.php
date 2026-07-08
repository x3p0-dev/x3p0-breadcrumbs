<?php

/**
 * Query registrar class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query;

use ReflectionException;
use X3P0\Breadcrumbs\Packages\Framework\Contracts\Bootable;

/**
 * Seeds the registry with the built-in query types on boot. Iterates the
 * `QueryType` enum, mapping each case's string key to its concrete class, and
 * skips any key a third party has already registered so custom overrides win.
 */
final class QueryRegistrar implements Bootable
{
	/**
	 * Stores the registry to be seeded with the built-in query types.
	 */
	public function __construct(
		private readonly QueryRegistry $registry
	) {}

	/**
	 * @inheritDoc
	 * @throws ReflectionException
	 */
	public function boot(): void
	{
		foreach (QueryType::cases() as $type) {
			if (! $this->registry->isRegistered($type->value)) {
				$this->registry->register($type->value, $type->className());
			}
		}
	}
}
