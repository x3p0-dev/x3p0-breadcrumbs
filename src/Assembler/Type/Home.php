<?php

/**
 * Home assembler.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler\Type;

use X3P0\Breadcrumbs\Assembler\Assembler;
use X3P0\Breadcrumbs\Crumb\CrumbType;

/**
 * Adds the home crumb. On a multisite sub-site, when the network crumb is
 * enabled in config, it adds both a network crumb and a crumb for the sub-site's
 * home instead of a single home crumb.
 */
final class Home extends Assembler
{
	/**
	 * @inheritDoc
	 */
	public function assemble(): void
	{
		if (
			is_multisite()
			&& $this->context->config()->showNetwork()
			&& ! is_main_site()
		) {
			$this->context->addCrumb(CrumbType::Network);
			$this->context->addCrumb(CrumbType::NetworkSite);
		} else {
			$this->context->addCrumb(CrumbType::Home);
		}
	}
}
