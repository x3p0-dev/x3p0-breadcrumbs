<?php

/**
 * Sensei LMS module query.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Extension\SenseiLms\Query;

use WP_Term;
use X3P0\Breadcrumbs\Assembler\AssemblerType;
use X3P0\Breadcrumbs\Crumb\CrumbType;
use X3P0\Breadcrumbs\Query\Query;

/**
 * Builds the trail for a module taxonomy archive — Courses → Course → Module.
 * Two Sensei behaviors keep the base taxonomy query from handling this: it
 * rewrites the archive's main query to list the module's lessons
 * (`post_type = lesson`), which leaves the queried object as something other
 * than the module term; and a module can belong to several courses at once,
 * scoped to one through a `course_id` query arg on the archive URL. This query
 * reads the course from that arg to root the trail and resolves the module
 * defensively from the request rather than the rewritten queried object.
 */
final class Module extends Query
{
	/**
	 * @inheritDoc
	 */
	public function query(): void
	{
		$this->context->assemble(AssemblerType::Home);

		// A module can belong to several courses, so its archive URL
		// scopes it to one through a `course_id` query arg; root the
		// trail at that course when present. This is a read-only display
		// value, so it is sanitized rather than nonce-checked.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$courseId = absint($_GET['course_id'] ?? 0);

		if (0 < $courseId && $course = get_post($courseId)) {
			$this->context->assemble(AssemblerType::Post, [
				'post' => $course
			]);
		}

		// Sensei rewrites the archive's main query to list lessons, so
		// the queried object is unreliable here: use it when it is a
		// term, and otherwise resolve the module from the taxonomy's
		// request var.
		$module = get_queried_object();

		if (! $module instanceof WP_Term) {
			$slug   = get_query_var('module');
			$module = $slug ? get_term_by('slug', $slug, 'module') : false;
		}

		if ($module instanceof WP_Term) {
			$this->context->addCrumb(CrumbType::Term, [
				'term' => $module
			]);
		}

		$this->context->assemble(AssemblerType::Paged);
	}
}
