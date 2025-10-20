<?php

/**
 * Assembler types registry.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler;

use TypeError;
use X3P0\Breadcrumbs\Contracts\{Assembler, AssemblerTypeRegistry};

class AssemblerTypes implements AssemblerTypeRegistry
{
	/**
	 * Stores the array of assembler classes.
	 */
	protected array $types = [];

	/**
	 * Allows registering a default set of assemblers.
	 */
	public function __construct(array $types = [])
	{
		foreach ($types as $name => $type) {
			$this->register($name, $type);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function register(string $name, string $type): void
	{
		if (! is_subclass_of($type, Assembler::class)) {
			throw new TypeError(esc_html(sprintf(
				// Translators: %s is a PHP class name.
				__('Only %s classes can be registered', 'x3p0-breadcrumbs'),
				Assembler::class
			)));
		}

		$this->types[$name] = $type;
	}

	/**
	 * {@inheritdoc}
	 */
	public function unregister(string $name): void
	{
		unset($this->types[$name]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function isRegistered(string $name): bool
	{
		return isset($this->types[$name]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function get(string $name, array $params = []): ?string
	{
		return $this->isRegistered($name) ? $this->types[$name] : null;
	}

	/**
	 * @deprecated 4.0.0
	 */
	public function add(string $name, string $type): void
	{
		$this->register($name, $type);
	}

	/**
	 * @deprecated 4.0.0
	 */
	public function remove(string $name): void
	{
		$this->unregister($name);
	}

	/**
	 * @deprecated 4.0.0
	 */
	public function has(string $name): bool
	{
		return $this->isRegistered($name);
	}
}
