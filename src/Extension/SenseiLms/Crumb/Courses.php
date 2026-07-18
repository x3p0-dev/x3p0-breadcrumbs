<?php

/**
 * Sensei LMS courses crumb.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Extension\SenseiLms\Crumb;

use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Crumb\Crumb;

use function Sensei;

/**
 * Crumb representing the Sensei LMS courses page. It replaces the course post
 * type archive crumb wherever it appears — the course archive itself, single
 * courses, and as the root of the lesson and quiz trails. Its label and URL
 * come from the configured courses page, falling back to the decorated crumb
 * when no courses page is set.
 */
final class Courses extends Crumb
{
	/**
	 * Wraps the crumb this decorates so the label and URL can fall back to
	 * it when no courses page is configured.
	 */
	public function __construct(
		BreadcrumbsContext $context,
		private readonly Crumb $decoratedCrumb
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function getLabel(): string
	{
		$pageId = (int) Sensei()->settings->get('course_page');

		if (0 < $pageId && $title = get_the_title($pageId)) {
			return $title;
		}

		return $this->decoratedCrumb->getLabel();
	}

	/**
	 * @inheritDoc
	 */
	public function getUrl(): string
	{
		$pageId = (int) Sensei()->settings->get('course_page');

		if (0 < $pageId && $url = get_permalink($pageId)) {
			return $url;
		}

		return $this->decoratedCrumb->getUrl();
	}
}
