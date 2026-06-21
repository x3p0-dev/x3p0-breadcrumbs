<?php

/**
 * Breadcrumbs implementation.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

use X3P0\Breadcrumbs\Assembler\AssemblerFactory;
use X3P0\Breadcrumbs\Crumb\{CrumbCollection, CrumbFactory};
use X3P0\Breadcrumbs\Query\{QueryFactory, QueryType};

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
		'is_404'        => QueryType::Error404,
		'is_front_page' => QueryType::FrontPage,
		'is_home'       => QueryType::Home,
		'is_singular'   => QueryType::Singular,
		'is_archive'    => QueryType::Archive,
		'is_search'     => QueryType::Search
	];

	/**
	 * Sets up initial object state.
	 */
	public function __construct(
		private readonly QueryFactory      $queryFactory,
		private readonly AssemblerFactory  $assemblerFactory,
		private readonly CrumbFactory      $crumbFactory,
		private readonly BreadcrumbsConfig $config
	) {}

	/**
	 * Generates a crumb collection.
	 */
	public function generate(): CrumbCollection
	{
		// Create the context that will be passed to queries/assemblers
		$context = new BreadcrumbsContext(
			crumbs:           new CrumbCollection(),
			queryFactory:     $this->queryFactory,
			assemblerFactory: $this->assemblerFactory,
			crumbFactory:     $this->crumbFactory,
			config:           $this->config
		);

		// Allow plugin developers to hook in and filter the type of
		// query class to call.
		$queryType = apply_filters(
			'x3p0/breadcrumbs/resolve/query-type',
			$this->resolveQueryType()?->value
		);

		if ($queryType) {
			$context->query($queryType);
		}

		return $context->crumbs();
	}

	/**
	 * Loop through the query conditionals and call the mapped query class.
	 */
	private function resolveQueryType(): ?QueryType
	{
		foreach (self::QUERY_CONDITIONALS as $tag => $type) {
			if (call_user_func($tag)) {
				return $type;
			}
		}

		return null;
	}
}
