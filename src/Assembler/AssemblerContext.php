<?php

/**
 * Assembler context.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler;

use X3P0\Breadcrumbs\BreadcrumbsConfig;
use X3P0\Breadcrumbs\Crumb\Crumb;
use X3P0\Breadcrumbs\Crumb\CrumbBuilder;
use X3P0\Breadcrumbs\Crumb\CrumbCollection;
use X3P0\Breadcrumbs\Crumb\CrumbDefinition;

/**
 * Everything an `Assembler` can reach: delegate to another assembler, build a
 * crumb, or add one to the shared trail.
 *
 * `makeCrumb()`/`addCrumb()` forward to the composed `CrumbBuilder` rather
 * than reimplementing them — the same reason `QueryContext` forwards to this
 * class instead of reimplementing `assemble()`. `$config` and `$crumbs` are
 * re-exposed as public properties, read from the same `CrumbBuilder`, so
 * subclasses and `QueryContext` (which composes this class) can read them
 * directly without reaching into `$crumbBuilder` themselves.
 */
final class AssemblerContext
{
	/**
	 * Read-only config, re-exposed from the composed `CrumbBuilder` so
	 * subclasses and `QueryContext` can read it directly.
	 */
	public readonly BreadcrumbsConfig $config;

	/**
	 * The mutable crumb collection accumulated so far, re-exposed from the
	 * composed `CrumbBuilder` so subclasses and `QueryContext` can read it
	 * directly.
	 */
	public readonly CrumbCollection $crumbs;

	/**
	 * Stores the factory assemblers are resolved through and the composed
	 * `CrumbBuilder` that `makeCrumb()`/`addCrumb()` forward to, then reads
	 * `$config` and `$crumbs` from it for direct, public access.
	 */
	public function __construct(
		private readonly AssemblerFactory $assemblerFactory,
		private readonly CrumbBuilder     $crumbBuilder
	) {
		$this->config = $crumbBuilder->config;
		$this->crumbs = $crumbBuilder->crumbs;
	}

	/**
	 * Dispatches an assembler by type, injecting this context so it can add
	 * to the trail or delegate further. Accepts any `AssemblerDefinition`
	 * enum, `Assembler` class string, or tagged slug. Does nothing when the
	 * type is unknown.
	 */
	public function assemble(AssemblerDefinition|string $type, array $params = []): void
	{
		$this->assemblerFactory->make($type, [
			'context' => $this,
			...$params
		])?->assemble();
	}

	/**
	 * Forwards to {@see CrumbBuilder::makeCrumb()}.
	 */
	public function makeCrumb(CrumbDefinition|string $type, array $params = []): ?Crumb
	{
		return $this->crumbBuilder->makeCrumb($type, $params);
	}

	/**
	 * Forwards to {@see CrumbBuilder::addCrumb()}.
	 */
	public function addCrumb(CrumbDefinition|string $type, array $params = []): void
	{
		$this->crumbBuilder->addCrumb($type, $params);
	}
}
