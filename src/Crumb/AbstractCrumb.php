<?php

/**
 * Crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb;

use X3P0\Breadcrumbs\Builder\Builder;

/**
 * Implements the `Crumb` interface and creates a custom crumb object.
 */
abstract class AbstractCrumb implements Crumb
{
	/**
	 * Creates a new crumb object.
	 */
	public function __construct(protected Builder $builder)
	{}

	/**
	 * {@inheritDoc}
	 */
	public function getType(): string
	{
		$type = $this->builder->environment()->crumbRegistry()->getTypeByClassName(
			get_class($this)
		);

		return $type ?: 'default';
	}

	/**
	 * {@inheritDoc}
	 */
	public function isType(string $type): bool
	{
		return $type === $this->getType();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getUrl(): string
	{
		return add_query_arg([]);
	}
}
