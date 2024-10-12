<?php

/**
 * Abstract assembler.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler;

use X3P0\Breadcrumbs\Contracts;

/**
 * Implements the `Assembler` interface and creates a custom Assembler object.
 */
abstract class Assembler implements Contracts\Assembler
{
	/**
	 * Creates a new query object.
	 */
	public function __construct(protected Contracts\Builder $builder)
	{}
}
