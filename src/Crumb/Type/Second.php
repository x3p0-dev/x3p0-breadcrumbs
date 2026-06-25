<?php

/**
 * Crumb representing a by-the-second time archive. Its label is the configured
 * "archive_second" string filled with the second, and its URL is built from
 * the date permastruct (extended with hour/minute/second), falling back to a
 * query-string archive URL when pretty permalinks are off.
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

final class Second extends Crumb
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
		if ($structure = $GLOBALS['wp_rewrite']->get_date_permastruct()) {
			$structure = trailingslashit($structure) . '%hour%/%minute%/%second%';

			// phpcs:disable Generic.Functions.FunctionCallArgumentSpacing.TooMuchSpaceAfterComma
			$structure = str_replace('%year%',     $year,   $structure);
			$structure = str_replace('%monthnum%', $month,  $structure);
			$structure = str_replace('%day%',      $day,    $structure);
			$structure = str_replace('%hour%',     $hour,   $structure);
			$structure = str_replace('%minute%',   $minute, $structure);
			$structure = str_replace('%second%',   $second, $structure);
			// phpcs:enable Generic.Functions.FunctionCallArgumentSpacing.TooMuchSpaceAfterComma

			return home_url(user_trailingslashit($structure, 'second'));
		}

		return home_url('?m=' . $year . $month . $day . $hour . $minute . $second);
	}
}
