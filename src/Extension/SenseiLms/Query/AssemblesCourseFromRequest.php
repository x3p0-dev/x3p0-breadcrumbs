<?php

/**
 * Assembles a course crumb from the request.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Extension\SenseiLms\Query;

use X3P0\Breadcrumbs\Assembler\AssemblerType;
use X3P0\Breadcrumbs\Extension\SenseiLms\SenseiLms;
use X3P0\Breadcrumbs\Query\QueryContext;

/**
 * Shared by the Sensei queries that scope themselves to a course passed as a
 * `course_id` query arg — the module archive and the course completed page.
 * Assumes the composing class is a `Query`, reading its `$context` property.
 *
 * @property QueryContext $context
 */
trait AssemblesCourseFromRequest
{
	/**
	 * Assembles the course named in the `course_id` request var into the
	 * trail, when present.
	 */
	protected function assembleCourseFromRequest(): void
	{
		// This is a read-only display value, so it is sanitized rather
		// than nonce-checked.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$courseId = absint($_GET[SenseiLms::COURSE_ID_VAR] ?? 0);

		if (0 < $courseId && $course = get_post($courseId)) {
			$this->context->assemble(AssemblerType::Post, [
				'post' => $course
			]);
		}
	}
}
