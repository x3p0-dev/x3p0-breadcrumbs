<?php

/**
 * Day crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2024 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb;

use WP_Post;
use X3P0\Breadcrumbs\Contracts\Builder;

class Day extends Crumb
{
	/**
	 * {@inheritdoc}
	 */
	public function __construct(
		protected Builder $builder,
		protected ?WP_Post $post = null
	) {
		parent::__construct($this->builder);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getLabel(): string
	{
		return sprintf(
			$this->builder->getLabel('archive_day'),
			get_the_time(
				esc_html_x('j', 'daily archives date format', 'x3p0-breadcrumbs'),
				$this->post
			)
		);
	}

	/**
	 * {@inheritdoc}
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
