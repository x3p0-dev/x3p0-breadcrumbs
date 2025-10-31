<?php

/**
 * Home assembler.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler\Type;

use X3P0\Breadcrumbs\Assembler\AbstractAssembler;

/**
 * Assembles the blog homepage crumb(s). For multisite, this may include showing
 * both the network and homepage of the sub-site crumb.
 */
final class Home extends AbstractAssembler
{
	/**
	 * {@inheritdoc}
	 */
	public function assemble(): void
	{
		if (
			is_multisite()
			&& $this->context->config()->showNetwork()
			&& ! is_main_site()
		) {
			$this->context->addCrumb('network');
			$this->context->addCrumb('network-site');
		} else {
			$this->context->addCrumb('home');
		}
	}
}
