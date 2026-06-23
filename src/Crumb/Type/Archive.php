<?php

/**
 * Crumb for a generic "Archives" label. Carries no URL; it acts as a
 * non-linking parent for the various archive views.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Type;

use X3P0\Breadcrumbs\Crumb\Crumb;

final class Archive extends Crumb
{
	/**
	 * @inheritDoc
	 */
	public function getLabel(): string
	{
		return $this->context->config()->getLabel('archives');
	}
}
