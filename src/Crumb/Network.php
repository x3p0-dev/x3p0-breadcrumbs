<?php

/**
 * Network crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Crumb;

class Network extends Base
{
	/**
	 * {@inheritdoc}
	 */
	public function label(): string
	{
		return $this->breadcrumbs->label('home');
	}

	/**
	 * {@inheritdoc}
	 */
	public function url(): string
	{
		return network_home_url();
	}

	/**
	 * {@inheritdoc}
	 */
	public function visuallyHidden(): bool
	{
		return ! $this->breadcrumbs->option('show_home_label');
	}
}
