<?php

/**
 * Breadcrumbs implementation.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

use X3P0\Breadcrumbs\Assembler\AssemblerFactory;
use X3P0\Breadcrumbs\Crumb\CrumbCollection;
use X3P0\Breadcrumbs\Crumb\CrumbFactory;
use X3P0\Breadcrumbs\Query\QueryFactory;

/**
 * A wrapper around the query, assembler, and crumb classes that takes a config
 * and generates a crumbs collection.
 */
final class Breadcrumbs
{
	/**
	 * Maps WordPress conditionals to default `Query` classes.
	 */
	protected const QUERY_CONDITIONALS = [
		'is_404'        => 'error-404',
		'is_front_page' => 'front-page',
		'is_home'       => 'home',
		'is_singular'   => 'singular',
		'is_archive'    => 'archive',
		'is_search'     => 'search'
	];

	/**
	 * Sets up initial object state.
	 */
	public function __construct(
		private QueryFactory      $queryFactory,
		private AssemblerFactory  $assemblerFactory,
		private CrumbFactory      $crumbFactory,
		private BreadcrumbsConfig $config
	) {}

	/**
	 * Generates a crumb collection.
	 */
	public function generate(): CrumbCollection
	{
		$crumbs = new CrumbCollection();

		// Create the context that will be passed to queries/assemblers
		$context = new BreadcrumbsContext(
			crumbs:           $crumbs,
			queryFactory:     $this->queryFactory,
			assemblerFactory: $this->assemblerFactory,
			crumbFactory:     $this->crumbFactory,
			config:           $this->config
		);

		if ($queryType = $this->resolveQueryType()) {
			$context->query($queryType);
		}

		return $crumbs;
	}

	/**
	 * Loop through the query conditionals and call the mapped query class.
	 */
	private function resolveQueryType(): ?string
	{
		foreach (static::QUERY_CONDITIONALS as $tag => $type) {
			if (call_user_func($tag)) {
				return $type;
			}
		}

		return null;
	}
}
