<?php

/**
 * Hour crumb class.
 *
 * Creates the minute + hour archive crumb.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Type;

use WP_Post;
use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Crumb\AbstractCrumb;

final class Hour extends AbstractCrumb
{
	/**
	 * @inheritDoc
	 */
	public function __construct(
		BreadcrumbsContext $context,
		public readonly ?WP_Post $post = null
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function getLabel(): string
	{
		return sprintf(
			$this->context->config()->getLabel('archive_hour'),
			get_the_time(
				esc_html_x('H', 'hour archives time format', 'x3p0-breadcrumbs'),
				$this->post
			)
		);
	}

	/**
	 * @inheritDoc
	 */
	public function getUrl(): string
	{
		$year  = get_the_time('Y', $this->post);
		$month = zeroise(absint(get_the_time('m', $this->post)), 2);
		$day   = zeroise(absint(get_the_time('d', $this->post)), 2);
		$hour  = zeroise(absint(get_the_time('H', $this->post)), 2);

		// WordPress doesn't have an hour structure function, so we're
		// building off the date structure.
		if ($structure = $GLOBALS['wp_rewrite']->get_date_permastruct()) {
			$structure = trailingslashit($structure) . '%hour%';

			// phpcs:disable Generic.Functions.FunctionCallArgumentSpacing.TooMuchSpaceAfterComma
			$structure = str_replace('%year%',     $year,  $structure);
			$structure = str_replace('%monthnum%', $month, $structure);
			$structure = str_replace('%day%',      $day,   $structure);
			$structure = str_replace('%hour%',     $hour,  $structure);
			// phpcs:enable Generic.Functions.FunctionCallArgumentSpacing.TooMuchSpaceAfterComma

			return home_url(user_trailingslashit($structure, 'hour'));
		}

		return home_url('?m=' . $year . $month . $day . $hour);
	}
}
