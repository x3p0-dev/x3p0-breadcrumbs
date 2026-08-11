<?php

/**
 * Sensei LMS course completed query.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Extension\SenseiLms\Query;

use WP_Exception;
use WP_Post;
use X3P0\Breadcrumbs\Assembler\AssemblerType;
use X3P0\Breadcrumbs\Crumb\CrumbType;

/**
 * Builds the trail for the course completed page — Courses → Course → Course
 * Completed. The page is a single configured page that Sensei shows for a
 * specific course, passed as a `course_id` query arg, so the base singular
 * query would render it without that course context. This query roots the trail
 * at the course (which the `CrumbsBuilt` relabel roots at the courses page) and
 * adds the page itself as the leaf.
 */
final class CourseCompleted extends CourseScopedQuery
{
	/**
	 * @inheritDoc
	 * @throws WP_Exception
	 */
	public function query(): void
	{
		$page = $this->queriedObject(WP_Post::class);

		$this->context->assemble(AssemblerType::Home);

		$this->assembleCourseFromRequest();

		if ($page instanceof WP_Post) {
			$this->context->addCrumb(CrumbType::Post, [
				'post' => $page
			]);
		}

		$this->context->assemble(AssemblerType::Paged);
	}
}
