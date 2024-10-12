<?php

/**
 * Builders collection class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Builder;

use TypeError;
use X3P0\Breadcrumbs\Contracts;

class Builders implements Contracts\Builders
{
	/**
	 * Stores the array of builder classes.
	 *
	 * @var class-string<Contracts\Builder>
	 */
	protected array $builders = [];

	/**
	 * Allows registering a default set of builders.
	 */
	public function __construct(array $builders = [])
	{
		foreach ($builders as $name => $class) {
			$this->add($name, $class);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function add(string $name, string $query): void
	{
		if (! is_subclass_of($query, Contracts\Builder::class)) {
			throw new TypeError(sprintf(
				__('Only %s classes can be registered', 'x3p0-ideas'),
				Contracts\Builder::class
			));
		}

		$this->builders[$name] = $query;
	}

	/**
	 * {@inheritdoc}
	 */
	public function remove(string $name): void
	{
		unset($this->builders[$name]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function has(string $name): bool
	{
		return isset($this->builders[$name]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function get(string $name, array $params = []): ?Contracts\Builder
	{
		if ($this->has($name)) {
			$builder = $this->builders[$name];
			return new $builder(...$params);
		}

		return null;
	}
}
