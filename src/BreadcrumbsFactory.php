<?php

/**
 * Breadcrumbs factory.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

use X3P0\Breadcrumbs\Assembler\AssemblerFactory;
use X3P0\Breadcrumbs\Crumb\CrumbFactory;
use X3P0\Breadcrumbs\Query\QueryFactory;

/**
 * Factory class for making breadcrumbs objects.
 */
final class BreadcrumbsFactory
{
	/**
	 * Sets up the initial factory state.
	 */
	public function __construct(
		private readonly QueryFactory     $queryFactory,
		private readonly AssemblerFactory $assemblerFactory,
		private readonly CrumbFactory     $crumbFactory,
	) {}

	/**
	 * Makes a breadcrumbs object.
	 */
	public function make(BreadcrumbsConfig $config): Breadcrumbs
	{
		return new Breadcrumbs(
			queryFactory:     $this->queryFactory,
			assemblerFactory: $this->assemblerFactory,
			crumbFactory:     $this->crumbFactory,
			config:           $config
		);
	}
}
