<?php

/**
 * Sensei LMS quiz query.
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
use X3P0\Breadcrumbs\Extension\SenseiLms\SenseiLms;
use X3P0\Breadcrumbs\Query\Query;

use function Sensei;

/**
 * Builds the trail for a single quiz, nesting it beneath its lesson and, in
 * turn, that lesson's course and module — Courses → Course → Module → Lesson →
 * Quiz. Sensei ties a quiz to its lesson through post meta (`_quiz_lesson`), so
 * the base singular query would otherwise render the quiz with no context.
 */
final class Quiz extends Query
{
	/**
	 * @inheritDoc
	 * @throws WP_Exception
	 */
	public function query(): void
	{
		$quiz = $this->queriedObject(WP_Post::class);

		$this->context->assemble(AssemblerType::Home);

		// Skip the quiz steps when the queried object is not a post, so
		// a query left in an unexpected state degrades to a safe trail.
		if ($quiz instanceof WP_Post) {
			$lessonId = (int) Sensei()->quiz->get_lesson_id($quiz->ID);

			if (0 < $lessonId && $lesson = get_post($lessonId)) {
				$this->context->assemble(SenseiLms::ASSEMBLER_LESSON_ANCESTRY, [
					'lesson' => $lesson
				]);

				$this->context->addCrumb(CrumbType::Post, [
					'post' => $lesson
				]);
			}

			$this->context->addCrumb(CrumbType::Post, [
				'post' => $quiz
			]);
		}

		$this->context->assemble(AssemblerType::Paged);
	}
}
