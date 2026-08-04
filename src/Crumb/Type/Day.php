<?php

/**
 * Day crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Type;

use WP_Post;
use X3P0\Breadcrumbs\BreadcrumbsConfig;
use X3P0\Breadcrumbs\Crumb\Crumb;
use X3P0\Breadcrumbs\BreadcrumbsLabel;
use X3P0\Breadcrumbs\Packages\Framework\Container\Attributes\NoAutowire;

/**
 * Crumb for a daily archive. Labels with the day of the month and links to the
 * day archive URL.
 */
final class Day extends Crumb
{
	/**
	 * @inheritDoc
	 */
	public function __construct(
		BreadcrumbsConfig $config,
		#[NoAutowire] public readonly ?WP_Post $post = null
	) {
		parent::__construct(config: $config);
	}

	/**
	 * @inheritDoc
	 */
	public function getSlug(): string
	{
		return 'day';
	}

	/**
	 * @inheritDoc
	 */
	public function getLabel(): string
	{
		return sprintf(
			$this->config->getLabel(BreadcrumbsLabel::ArchiveDay),
			get_the_time(
				esc_html_x('j', 'daily archives date format', 'x3p0-breadcrumbs'),
				$this->post
			)
		);
	}

	/**
	 * @inheritDoc
	 */
	public function getUrl(): string
	{
		return get_day_link(
			get_the_time('Y', $this->post),
			get_the_time('m', $this->post),
			get_the_time('d', $this->post)
		);
	}
}
