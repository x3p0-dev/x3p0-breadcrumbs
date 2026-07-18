<?php

/**
 * Sensei LMS course results query.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Extension\SenseiLms\Query;

use WP_Post;
use X3P0\Breadcrumbs\Assembler\AssemblerType;
use X3P0\Breadcrumbs\Crumb\CrumbType;
use X3P0\Breadcrumbs\Query\Query;

/**
 * Builds the trail for a course results page — Courses → Course → Results.
 * Sensei serves this from a custom rewrite endpoint (`/{courses}/{course}/
 * results`) that sets only a `course_results` query var holding the course
 * slug, with no standard queried object, so the base resolver cannot classify
 * it. This query reads the slug to root the trail at the course (which the
 * `CrumbsBuilt` relabel roots at the courses page) and adds the results leaf.
 */
final class CourseResults extends Query
{
	/**
	 * @inheritDoc
	 */
	public function query(): void
	{
		$this->context->assemble(AssemblerType::Home);

		// The `course_results` query var holds the course slug; resolve
		// the course to root the trail at it.
		$slug   = get_query_var('course_results');
		$course = $slug ? get_page_by_path($slug, OBJECT, 'course') : null;

		if ($course instanceof WP_Post) {
			$this->context->assemble(AssemblerType::Post, [
				'post' => $course
			]);
		}

		$this->context->addCrumb(CrumbType::Custom, [
			'label' => __('Results', 'x3p0-breadcrumbs')
		]);

		$this->context->assemble(AssemblerType::Paged);
	}
}
