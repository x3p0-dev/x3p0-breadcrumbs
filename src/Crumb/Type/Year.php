<?php

/**
 * Year crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Type;

use WP_Post;
use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Crumb\Crumb;
use X3P0\Breadcrumbs\BreadcrumbsLabel;

/**
 * Crumb representing a yearly time archive. Its label is the configured
 * "archive_year" string filled with the year, and its URL is the year
 * archive link.
 */
final class Year extends Crumb
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
			$this->context->config()->getLabel(BreadcrumbsLabel::ArchiveYear),
			get_the_time(esc_html_x(
				'Y',
				'yearly archives date format',
				'x3p0-breadcrumbs'
			), $this->post)
		);
	}

	/**
	 * @inheritDoc
	 */
	public function getUrl(): string
	{
		return get_year_link(get_the_time('Y', $this->post));
	}
}
