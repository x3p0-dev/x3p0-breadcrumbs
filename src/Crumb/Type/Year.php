<?php

/**
 * Year crumb class.
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

final class Year extends AbstractCrumb
{
	/**
	 * {@inheritdoc}
	 */
	public function __construct(
		protected BreadcrumbsContext $context,
		protected ?WP_Post $post = null
	) {
		parent::__construct(...func_get_args());
	}

	/**
	 * {@inheritdoc}
	 */
	public function getLabel(): string
	{
		return sprintf(
			$this->context->config()->getLabel('archive_year'),
			get_the_time(esc_html_x(
				'Y',
				'yearly archives date format',
				'x3p0-breadcrumbs'
			), $this->post)
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getUrl(): string
	{
		return get_year_link(get_the_time('Y', $this->post));
	}
}
