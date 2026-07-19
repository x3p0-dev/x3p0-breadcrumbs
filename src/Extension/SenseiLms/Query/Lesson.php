<?php

/**
 * Sensei LMS lesson query.
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
use X3P0\Breadcrumbs\Extension\SenseiLms\Assembler\LessonAncestry;
use X3P0\Breadcrumbs\Query\Query;

/**
 * Builds the trail for a single lesson, nesting it beneath its course and
 * module — Courses → Course → Module → Lesson — rather than the flat trail the
 * base singular query would produce from the lesson alone.
 */
final class Lesson extends Query
{
	/**
	 * @inheritDoc
	 * @throws WP_Exception
	 */
	public function query(): void
	{
		$lesson = $this->queriedObject(WP_Post::class);

		$this->context->assemble(AssemblerType::Home);

		// Skip the lesson steps when the queried object is not a post,
		// so a query left in an unexpected state degrades to a safe trail.
		if ($lesson instanceof WP_Post) {
			$this->context->assemble(LessonAncestry::class, [
				'lesson' => $lesson
			]);

			$this->context->addCrumb(CrumbType::Post, [
				'post' => $lesson
			]);
		}

		$this->context->assemble(AssemblerType::Paged);
	}
}
