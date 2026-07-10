<?php

/**
 * Network site crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Type;

use X3P0\Breadcrumbs\Crumb\Crumb;

/**
 * Crumb for an individual site within a multisite network. Labels with the
 * site name and links to the site's home URL.
 */
final class NetworkSite extends Crumb
{
	/**
	 * @inheritDoc
	 */
	public function getLabel(): string
	{
		return get_bloginfo('name');
	}

	/**
	 * @inheritDoc
	 */
	public function getUrl(): string
	{
		return user_trailingslashit(home_url());
	}
}
