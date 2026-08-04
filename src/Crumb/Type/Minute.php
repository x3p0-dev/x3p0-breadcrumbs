<?php

/**
 * Minute crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Type;

use X3P0\Breadcrumbs\BreadcrumbsLabel;

/**
 * Crumb for a minute archive. Labels with the minute and builds a minute
 * archive URL from the date permastruct, since WordPress has no minute link
 * function.
 */
final class Minute extends TimeArchive
{
	/**
	 * @inheritDoc
	 */
	public function getSlug(): string
	{
		return 'minute';
	}

	/**
	 * @inheritDoc
	 */
	public function getLabel(): string
	{
		return sprintf(
			$this->config->getLabel(BreadcrumbsLabel::ArchiveMinute),
			get_the_time(
				esc_html_x('i', 'minute archives time format', 'x3p0-breadcrumbs'),
				$this->post
			)
		);
	}

	/**
	 * @inheritDoc
	 */
	protected function formats(): array
	{
		return ['hour' => 'H', 'minute' => 'i'];
	}
}
