<?php

/**
 * Assemblers collection class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler;

use TypeError;
use X3P0\Breadcrumbs\Contracts;

class Assemblers implements Contracts\Assemblers
{
	/**
	 * Stores the array of assembler classes.
	 */
	protected array $assemblers = [];

	/**
	 * Allows registering a default set of assemblers.
	 */
	public function __construct(array $assemblers = [])
	{
		foreach ($assemblers as $name => $class) {
			$this->add($name, $class);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function add(string $name, string $assembler): void
	{
		if (! is_subclass_of($assembler, Contracts\Assembler::class)) {
			throw new TypeError(esc_html(sprintf(
				// Translators: %s is a PHP class name.
				__('Only %s classes can be registered', 'x3p0-ideas'),
				Contracts\Assembler::class
			)));
		}

		$this->assemblers[$name] = $assembler;
	}

	/**
	 * {@inheritdoc}
	 */
	public function remove(string $name): void
	{
		unset($this->assemblers[$name]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function has(string $name): bool
	{
		return isset($this->assemblers[$name]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function get(string $name, array $params = []): ?Contracts\Assembler
	{
		if ($this->has($name)) {
			$assembler = $this->assemblers[$name];
			return new $assembler(...$params);
		}

		return null;
	}
}
