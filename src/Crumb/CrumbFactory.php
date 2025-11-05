<?php

/**
 * Crumb factory.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb;

/**
 * Factory class used for creating new crumb instances from a registry of
 * crumb types.
 */
final class CrumbFactory
{
	/**
	 * Sets up the initial object state.
	 */
	public function __construct(private CrumbRegistry $crumbRegistry)
	{}

	/**
	 * Creates an instance of a crumb object.
	 */
	public function make(string $key, array $params = []): ?Crumb
	{
		if ($crumb = $this->crumbRegistry->get($key)) {
			return new $crumb(...$params);
		}

		return null;
	}
}
