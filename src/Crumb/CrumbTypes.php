<?php

/**
 * Crumb types registry.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb;

use TypeError;
use X3P0\Breadcrumbs\Contracts;

class CrumbTypes implements Contracts\CrumbRegistry
{
	/**
	 * Stores the array of crumb classes.
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
	public function add(string $name, string $crumb): void
	{
		if (! is_subclass_of($crumb, Contracts\Crumb::class)) {
			throw new TypeError(esc_html(sprintf(
				// Translators: %s is a PHP class name.
				__('Only %s classes can be registered', 'x3p0-ideas'),
				Contracts\Crumb::class
			)));
		}

		$this->crumbs[$name] = $crumb;
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
