<?php

/**
 * Second crumb class.
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

final class Second extends AbstractCrumb
{
	/**
	 * @inheritDoc
	 */
	public function __construct(
		protected BreadcrumbsContext $context,
		protected ?WP_Post $post = null
	) {
		parent::__construct(...func_get_args());
	}

	/**
	 * @inheritDoc
	 */
	public function getLabel(): string
	{
		return sprintf(
			$this->context->config()->getLabel('archive_second'),
			get_the_time(
				esc_html_x('s', 'minute archives time format', 'x3p0-breadcrumbs'),
				$this->post
			)
		);
	}

	/**
	 * @inheritDoc
	 */
	public function getUrl(): string
	{
		$year   = get_the_time('Y', $this->post);
		$month  = zeroise(absint(get_the_time('m', $this->post)), 2);
		$day    = zeroise(absint(get_the_time('d', $this->post)), 2);
		$hour   = zeroise(absint(get_the_time('H', $this->post)), 2);
		$minute = zeroise(absint(get_the_time('i', $this->post)), 2);
		$second = zeroise(absint(get_the_time('s', $this->post)), 2);

		// WordPress doesn't have a second structure function, so we're
		// building off the date structure.
		$structure = $GLOBALS['wp_rewrite']->get_date_permastruct();

		if (! empty($structure)) {
			$structure = trailingslashit($structure) . '%hour%/%minute%/%second%';

			$structure = str_replace('%year%',     $year,    $structure);
			$structure = str_replace('%monthnum%', $month,   $structure);
			$structure = str_replace('%day%',      $day,     $structure);
			$structure = str_replace('%hour%',     $hour,    $structure);
			$structure = str_replace('%minute%',   $minute,  $structure);
			$structure = str_replace('%second%',   $second,  $structure);

			return home_url(user_trailingslashit($structure, 'second'));
		}

		return home_url('?m=' . $year . $month . $day . $hour . $minute . $second);
	}
}
