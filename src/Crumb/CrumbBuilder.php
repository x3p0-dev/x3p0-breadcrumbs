<?php

/**
 * Crumb builder.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb;

use X3P0\Breadcrumbs\BreadcrumbsConfig;

/**
 * The crumb-building capability alone. This is the single place `makeCrumb()`
 * and `addCrumb()` are implemented — `AssemblerContext` composes an instance of
 * this rather than reimplementing the same two methods, and `CrumbsBuilt`
 * listeners get one directly. `$config` and `$crumbs` are public so
 * `AssemblerContext` can re-expose them.
 */
final class CrumbBuilder
{
	/**
	 * Read-only trail config, re-exposed from the context so
	 * `AssemblerContext` can re-expose it in turn without reaching through
	 * `$context` itself.
	 */
	public readonly BreadcrumbsConfig $config;

	/**
	 * Stores the factory crumbs are built through, the shared context every
	 * crumb is built against, and the mutable crumb collection crumbs are
	 * appended to, then re-exposes the context's trail config.
	 */
	public function __construct(
		private readonly CrumbFactory    $crumbFactory,
		public  readonly CrumbContext    $context,
		public  readonly CrumbCollection $crumbs
	) {
		$this->config = $context->config;
	}

	/**
	 * Builds a crumb by type and returns it without adding it to the
	 * collection. Returns null for an unknown type.
	 */
	public function makeCrumb(CrumbDefinition|string $type, array $params = []): ?Crumb
	{
		return $this->crumbFactory->make($type, [
			'context' => $this->context,
			...$params
		]);
	}

	/**
	 * Builds a crumb by type and appends it to the shared collection. Does
	 * nothing when the type is unknown.
	 */
	public function addCrumb(CrumbDefinition|string $type, array $params = []): void
	{
		if ($crumb = $this->makeCrumb($type, $params)) {
			$this->crumbs->push($crumb);
		}
	}
}
