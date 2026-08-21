<?php

/**
 * Network crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Type;

use X3P0\Breadcrumbs\BreadcrumbsLabel;
use X3P0\Breadcrumbs\Crumb\Crumb;
use X3P0\Breadcrumbs\Icon\IconOptionKey;

/**
 * Crumb for the multisite network home. Uses the configured home label and
 * links to the network home URL.
 */
final class Network extends Crumb
{
	/**
	 * @inheritDoc
	 */
	public function getSlug(): string
	{
		return 'network';
	}

	/**
	 * Resolves {@see IconOptionKey::Home} rather than an option of its own. On
	 * a network this
	 * crumb *is* the home of the trail — it takes the home label and links to
	 * the network home — so a site owner configuring the home icon has
	 * configured this crumb, and a second option beside it would only be a
	 * control that quietly replaces the one they already set.
	 *
	 * @inheritDoc
	 */
	protected function iconOptionKey(): IconOptionKey
	{
		return IconOptionKey::Home;
	}

	/**
	 * @inheritDoc
	 */
	public function getLabel(): string
	{
		return $this->config->getLabel(BreadcrumbsLabel::Home);
	}

	/**
	 * @inheritDoc
	 */
	public function getUrl(): string
	{
		return network_home_url();
	}
}
