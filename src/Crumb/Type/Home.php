<?php

/**
 * Crumb for the site home. Outputs the configured home label and links to the
 * front-page URL; typically the first crumb in the trail.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Type;

use X3P0\Breadcrumbs\Crumb\Crumb;

final class Home extends Crumb
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
		return user_trailingslashit(home_url());
	}
}
