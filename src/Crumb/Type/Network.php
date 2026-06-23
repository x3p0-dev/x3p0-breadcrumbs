<?php

/**
 * Crumb for the multisite network home. Uses the configured home label and
 * links to the network home URL.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Type;

use X3P0\Breadcrumbs\Crumb\Crumb;

final class Network extends Crumb
{
	/**
	 * @inheritDoc
	 */
	public function getLabel(): string
	{
		return $this->context->config()->getLabel('home');
	}

	/**
	 * @inheritDoc
	 */
	public function getUrl(): string
	{
		return network_home_url();
	}
}
