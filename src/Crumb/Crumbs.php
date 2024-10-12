<?php

/**
 * Crumbs collection class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb;

use TypeError;
use X3P0\Breadcrumbs\Contracts;

class Crumbs implements Contracts\Crumbs
{
	/**
	 * Stores the array of crumb classes.
	 *
	 * @var class-string<Contracts\Crumb>
	 */
	protected array $crumbs = [];

	/**
	 * Allows registering a default set of crumbs.
	 */
	public function __construct(array $crumbs = [])
	{
		foreach ($crumbs as $name => $class) {
			$this->add($name, $class);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function add(string $name, string $query): void
	{
		if (! is_subclass_of($query, Contracts\Crumb::class)) {
			throw new TypeError(sprintf(
				__('Only %s classes can be registered', 'x3p0-ideas'),
				Contracts\Crumb::class
			));
		}

		$this->crumbs[$name] = $query;
	}

	/**
	 * {@inheritdoc}
	 */
	public function remove(string $name): void
	{
		unset($this->crumbs[$name]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function has(string $name): bool
	{
		return isset($this->crumbs[$name]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function get(string $name, array $params = []): ?Contracts\Crumb
	{
		if ($this->has($name)) {
			$crumb = $this->crumbs[$name];
			return new $crumb(...$params);
		}

		return null;
	}
}
