<?php

/**
 * Front page query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query\Type;

use X3P0\Breadcrumbs\Assembler\AssemblerType;
use X3P0\Breadcrumbs\Query\Query;

/**
 * Builds the trail for the site's front page: the home step plus the paged step
 * (for paginated front-page content). The home step typically renders the only
 * visible crumb here.
 */
final class FrontPage extends Query
{
	/**
	 * @inheritDoc
	 */
	public function query(): void
	{
		$this->context->assemble(AssemblerType::Home);
		$this->context->assemble(AssemblerType::Paged);
	}
}
