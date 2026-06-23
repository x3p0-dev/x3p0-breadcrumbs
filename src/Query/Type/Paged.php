<?php

/**
 * Paged query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query\Type;

use X3P0\Breadcrumbs\Assembler\AssemblerType;
use X3P0\Breadcrumbs\Query\Query;

/**
 * Builds a minimal trail for generic paged/subsidiary requests that have no
 * more specific query: the home, rewrite-front, and paged steps.
 */
final class Paged extends Query
{
	/**
	 * @inheritDoc
	 */
	public function query(): void
	{
		$this->context->assemble(AssemblerType::Home);
		$this->context->assemble(AssemblerType::RewriteFront);
		$this->context->assemble(AssemblerType::Paged);
	}
}
