<?php

/**
 * Hour crumb class.
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
 * Crumb for an hourly archive. Labels with the hour and builds an hour archive
 * URL from the date permastruct, since WordPress has no hour link function.
 */
final class Hour extends TimeArchive
{
	/**
	 * @inheritDoc
	 */
	public function getSlug(): string
	{
		return 'hour';
	}

	/**
	 * @inheritDoc
	 */
	public function getLabel(): string
	{
		return sprintf(
			$this->config->getLabel(BreadcrumbsLabel::ArchiveHour),
			get_the_time(
				esc_html_x('H', 'hour archives time format', 'x3p0-breadcrumbs'),
				$this->post
			)
		);
	}

	/**
	 * @inheritDoc
	 */
	protected function formats(): array
	{
		return ['hour' => 'H'];
	}
}
