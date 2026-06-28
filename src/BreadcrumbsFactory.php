<?php

/**
 * Breadcrumbs factory.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

use X3P0\Breadcrumbs\Assembler\AssemblerFactory;
use X3P0\Breadcrumbs\Crumb\CrumbFactory;
use X3P0\Breadcrumbs\Query\QueryFactory;
use X3P0\Breadcrumbs\Query\QueryResolver;

/**
 * Builds `Breadcrumbs` instances, handing each one the query resolver and the
 * factories it needs to create the participants in the build pipeline (query,
 * assembler, and crumb objects) along with the config that controls how the
 * trail is built.
 */
final class BreadcrumbsFactory
{
	/**
	 * Stores the query resolver and the factories used to create the pipeline
	 * participants for the breadcrumbs objects this factory builds.
	 */
	public function __construct(
		private readonly QueryResolver    $queryResolver,
		private readonly QueryFactory     $queryFactory,
		private readonly AssemblerFactory $assemblerFactory,
		private readonly CrumbFactory     $crumbFactory,
	) {}

	/**
	 * Returns a new `Breadcrumbs` object wired with the query resolver, the
	 * pipeline factories, and the given config.
	 */
	public function make(BreadcrumbsConfig $config): Breadcrumbs
	{
		return new Breadcrumbs(
			queryResolver:    $this->queryResolver,
			queryFactory:     $this->queryFactory,
			assemblerFactory: $this->assemblerFactory,
			crumbFactory:     $this->crumbFactory,
			config:           $config
		);
	}
}
