<?php

/**
 * Minute crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Type;

use WP_Post;
use X3P0\Breadcrumbs\Contracts\Builder;
use X3P0\Breadcrumbs\Crumb\AbstractCrumb;

final class Minute extends AbstractCrumb
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
			$this->builder->getLabel('archive_minute'),
			get_the_time(
				esc_html_x('i', 'minute archives time format', 'x3p0-breadcrumbs'),
				$this->post
			)
		);
	}
}
