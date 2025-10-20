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

use X3P0\Breadcrumbs\Contracts;

class CrumbFactory implements Contracts\CrumbFactory
{
	/**
	 * Sets up the initial object state.
	 */
	public function __construct(
		private Contracts\CrumbTypeRegistry $crumbTypes
	) {}

	/**
	 * {@inheritDoc}
	 */
	public function make(string $name, array $params = []): ?Contracts\Crumb
	{
		if ($this->crumbTypes->has($name)) {
			$crumb = $this->crumbTypes->get($name);
			return new $crumb(...$params);
		}

		return null;
	}
}
