<?php

/**
 * Sensei LMS extension.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Extension\SenseiLms;

use ReflectionException;
use X3P0\Breadcrumbs\Assembler\AssemblerRegistry;
use X3P0\Breadcrumbs\Crumb\CrumbRegistry;
use X3P0\Breadcrumbs\Crumb\Event\CrumbsBuilt;
use X3P0\Breadcrumbs\Crumb\Type\PostType as PostTypeCrumb;
use X3P0\Breadcrumbs\Extension\Extension;
use X3P0\Breadcrumbs\Extension\SenseiLms\Assembler\LessonAncestry as LessonAncestryAssembler;
use X3P0\Breadcrumbs\Extension\SenseiLms\Crumb\Courses as CoursesCrumb;
use X3P0\Breadcrumbs\Extension\SenseiLms\Query\CourseCompleted as CourseCompletedQuery;
use X3P0\Breadcrumbs\Extension\SenseiLms\Query\CourseResults as CourseResultsQuery;
use X3P0\Breadcrumbs\Extension\SenseiLms\Query\LearnerProfile as LearnerProfileQuery;
use X3P0\Breadcrumbs\Extension\SenseiLms\Query\Lesson as LessonQuery;
use X3P0\Breadcrumbs\Extension\SenseiLms\Query\Module as ModuleQuery;
use X3P0\Breadcrumbs\Extension\SenseiLms\Query\Quiz as QuizQuery;
use X3P0\Breadcrumbs\Markup\Event\MarkupRendering;
use X3P0\Breadcrumbs\Query\Event\QueryTypeResolving;
use X3P0\Breadcrumbs\Query\QueryRegistry;

use function Sensei;

/**
 * Built-in Sensei LMS integration. The base queries already build correct
 * trails for the course archive, single courses, and the course taxonomies,
 * since a course is a public post type with an archive and the course
 * taxonomies are ordinary taxonomies — so the extension only relabels the
 * course post type archive crumb to read as the configured courses page. It
 * does this on the `CrumbsBuilt` event rather than by replacing the built-in
 * crumb class, so other extensions can relabel their own crumbs on the same
 * event without one overriding the others.
 *
 * The rest is the part core has no concept of: lessons, quizzes, module
 * archives, course results, course completion, and learner profiles. Sensei
 * models lesson and quiz parentage with post meta (`_lesson_course`,
 * `_quiz_lesson`) and a per-course module taxonomy rather than post parents,
 * rewrites the module archive's own query to list lessons, serves the course
 * results and learner profile pages from custom rewrite endpoints that carry
 * only a query var and no standard queried object, and shows the completed page
 * for a course passed as a `course_id` query arg — so the base queries cannot
 * build these trails. Each gets a custom query, rerouted to while resolving the
 * query type, that rebuilds the missing Course → Module → Lesson → Quiz
 * ancestry (or roots the course results and completion pages at their course
 * and the learner profile under home).
 */
final class SenseiLms extends Extension
{
	public const QUERY_LESSON              = 'sensei-lms/lesson';
	public const QUERY_QUIZ                = 'sensei-lms/quiz';
	public const QUERY_MODULE              = 'sensei-lms/module';
	public const QUERY_COURSE_RESULTS      = 'sensei-lms/course-results';
	public const QUERY_COURSE_COMPLETED    = 'sensei-lms/course-completed';
	public const QUERY_LEARNER_PROFILE     = 'sensei-lms/learner-profile';
	public const CRUMB_COURSES             = 'sensei-lms/courses';
	public const ASSEMBLER_LESSON_ANCESTRY = 'sensei-lms/lesson-ancestry';

	/**
	 * Stores the query and crumb registries the extension seeds its custom
	 * types into.
	 */
	public function __construct(
		private readonly QueryRegistry $queries,
		private readonly AssemblerRegistry $assemblers,
		private readonly CrumbRegistry $crumbs
	) {}

	/**
	 * @inheritDoc
	 */
	public function isActive(): bool
	{
		return function_exists('Sensei');
	}

	/**
	 * @inheritDoc
	 * @throws ReflectionException
	 */
	public function register(): void
	{
		// Register Sensei LMS query types.
		$this->queries->register(self::QUERY_LESSON, LessonQuery::class);
		$this->queries->register(self::QUERY_QUIZ, QuizQuery::class);
		$this->queries->register(self::QUERY_MODULE, ModuleQuery::class);
		$this->queries->register(self::QUERY_COURSE_RESULTS, CourseResultsQuery::class);
		$this->queries->register(self::QUERY_COURSE_COMPLETED, CourseCompletedQuery::class);
		$this->queries->register(self::QUERY_LEARNER_PROFILE, LearnerProfileQuery::class);

		// Register Sensei LMS assembler types.
		$this->assemblers->register(self::ASSEMBLER_LESSON_ANCESTRY, LessonAncestryAssembler::class);

		// Register Sensei LMS crumb types.
		$this->crumbs->register(self::CRUMB_COURSES, CoursesCrumb::class);
	}

	/**
	 * @inheritDoc
	 */
	public function getSubscribedEvents(): array
	{
		return [
			QueryTypeResolving::class => 'resolveQueryType',
			CrumbsBuilt::class        => 'relabelCourses',
			MarkupRendering::class    => 'showOnVirtualPages'
		];
	}

	/**
	 * Reroutes single lessons and quizzes, module archives, the course
	 * results and learner profile rewrite pages, and the course completed
	 * page to their custom query before the built-in queries would claim
	 * them, then stops propagation to keep the final say. Everything else —
	 * including the course archive, single courses, and course taxonomies,
	 * which the base queries handle — falls through untouched.
	 */
	public function resolveQueryType(QueryTypeResolving $event): void
	{
		$type = match (true) {
			is_singular('quiz')            => self::QUERY_QUIZ,
			is_singular('lesson')          => self::QUERY_LESSON,
			is_tax('module')               => self::QUERY_MODULE,
			$this->isCourseResultsPage()   => self::QUERY_COURSE_RESULTS,
			$this->isLearnerProfilePage()  => self::QUERY_LEARNER_PROFILE,
			$this->isCourseCompletedPage() => self::QUERY_COURSE_COMPLETED,
			default                        => null
		};

		if ($type) {
			$event->setQueryType($type);
			$event->stopPropagation();
		}
	}

	/**
	 * Replaces the course post type archive crumb with the courses crumb
	 * wherever it appears, so the archive reads as the configured courses
	 * page without overriding the built-in post type crumb class.
	 */
	public function relabelCourses(CrumbsBuilt $event): void
	{
		$event->crumbs->replaceInstanceWhere(
			PostTypeCrumb::class,
			static fn (PostTypeCrumb $crumb) => 'course' === $crumb->postType->name,
			static fn (PostTypeCrumb $crumb) => $event->context->makeCrumb(
				self::CRUMB_COURSES,
				['decoratedCrumb' => $crumb]
			)
		);
	}

	/**
	 * Forces the trail to render on Sensei's virtual, query-var-only pages.
	 * WordPress finds no post or archive for the course results and learner
	 * profile endpoints, so it falls back to flagging them as the front page,
	 * which the markup layer would otherwise treat as the homepage and
	 * suppress. These pages are not the front page, so front-page visibility
	 * is forced on for them alone, leaving the rest of the config untouched.
	 */
	public function showOnVirtualPages(MarkupRendering $event): void
	{
		if ($this->isVirtualPage()) {
			$event->config = $event->config->with([
				'showOnFront' => true
			]);
		}
	}

	/**
	 * Whether the current request is one of Sensei's virtual, query-var-only
	 * pages which carry no post or archive and so are misclassified as the
	 * front page.
	 */
	private function isVirtualPage(): bool
	{
		return $this->isLearnerProfilePage();
	}

	/**
	 * Whether the current request is Sensei's course results page, served
	 * from a rewrite endpoint that sets the `course_results` query var.
	 */
	private function isCourseResultsPage(): bool
	{
		return '' !== get_query_var('course_results');
	}

	/**
	 * Whether the current request is Sensei's learner profile page, served
	 * from a rewrite endpoint that sets the `learner_profile` query var.
	 */
	private function isLearnerProfilePage(): bool
	{
		return '' !== get_query_var('learner_profile');
	}

	/**
	 * Whether the current request is the configured course completed page.
	 * The page ID is guarded against zero so an unset setting does not match
	 * every page (`is_page(0)` is truthy on any singular page).
	 */
	private function isCourseCompletedPage(): bool
	{
		$pageId = (int) Sensei()->settings->get('course_completed_page');

		return 0 < $pageId && is_page($pageId);
	}
}
