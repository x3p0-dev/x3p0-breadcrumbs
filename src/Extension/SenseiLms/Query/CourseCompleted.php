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
use X3P0\Breadcrumbs\Query\Query;

/**
 * Builds the trail for the course completed page — Courses → Course → Course
 * Completed. The page is a single configured page that Sensei shows for a
 * specific course, passed as a `course_id` query arg, so the base singular
 * query would render it without that course context. This query roots the trail
 * at the course (which the `CrumbsBuilt` relabel roots at the courses page) and
 * adds the page itself as the leaf.
 */
final class CourseCompleted extends Query
{
	/**
	 * @inheritDoc
	 * @throws WP_Exception
	 */
	public function query(): void
	{
		$page = $this->queriedObject(WP_Post::class);

		$this->context->assemble(AssemblerType::Home);

		// The completed page is shown for a specific course, passed as
		// a `course_id` query arg; root the trail at that course when
		// present. This is a read-only display value, so it is sanitized
		// rather than nonce-checked.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$courseId = absint($_GET['course_id'] ?? 0);

		if (0 < $courseId && $course = get_post($courseId)) {
			$this->context->assemble(AssemblerType::Post, [
				'post' => $course
			]);
		}

		if ($page instanceof WP_Post) {
			$this->context->addCrumb(CrumbType::Post, [
				'post' => $page
			]);
		}

		$this->context->assemble(AssemblerType::Paged);
	}
}
