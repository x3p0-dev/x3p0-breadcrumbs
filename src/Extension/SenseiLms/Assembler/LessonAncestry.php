<?php

/**
 * Sensei LMS lesson ancestry assembler.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Extension\SenseiLms\Assembler;

use WP_Post;
use WP_Term;
use X3P0\Breadcrumbs\Assembler\Assembler;
use X3P0\Breadcrumbs\Assembler\AssemblerType;
use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Crumb\CrumbType;
use X3P0\Breadcrumbs\Packages\Framework\Container\Attributes\NoAutowire;

use function Sensei;

/**
 * Assembles the course ancestry shared by Sensei lessons and quizzes: the parent
 * course (which the `CrumbsBuilt` relabel roots at the courses page) and the
 * lesson's module when one is assigned. Sensei ties a lesson to its course
 * through post meta (`_lesson_course`) and to its module through a per-course
 * taxonomy term, neither of which is a post parent, so this rebuilds that chain
 * for the lesson and quiz queries to append their own leaf onto.
 */
final class LessonAncestry extends Assembler
{
	/**
	 * @inheritDoc
	 */
	public function __construct(
		BreadcrumbsContext $context,
		#[NoAutowire] private readonly WP_Post $lesson
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function assemble(): void
	{
		$courseId = (int) Sensei()->lesson->get_course_id($this->lesson->ID);

		if (0 < $courseId && $course = get_post($courseId)) {
			$this->context->assemble(AssemblerType::Post, [
				'post' => $course
			]);
		}

		// The module carries a per-course archive URL that a plain term
		// crumb would not reproduce, so add it as a custom crumb built
		// from the module's own name and URL.
		$module = Sensei()->modules->get_lesson_module($this->lesson->ID);

		if ($module instanceof WP_Term) {
			$this->context->addCrumb(CrumbType::Custom, [
				'label' => $module->name,
				'url'   => $module->url ?? ''
			]);
		}
	}
}
