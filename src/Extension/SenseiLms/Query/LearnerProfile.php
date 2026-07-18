<?php

/**
 * Sensei LMS learner profile query.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Extension\SenseiLms\Query;

use Sensei_Learner;
use WP_User;
use X3P0\Breadcrumbs\Assembler\AssemblerType;
use X3P0\Breadcrumbs\Crumb\CrumbType;
use X3P0\Breadcrumbs\Query\Query;

/**
 * Builds the trail for a learner profile page — a leaf under home labeled with
 * the learner's name. Sensei serves this from a custom rewrite endpoint
 * (`/{learner}/{nicename}`) that sets only a `learner_profile` query var
 * holding the user's nicename, with no standard queried object, so the base
 * resolver cannot classify it. Unlike course content, a learner profile is not
 * part of the course hierarchy, so it stands alone directly under home.
 */
final class LearnerProfile extends Query
{
	/**
	 * @inheritDoc
	 */
	public function query(): void
	{
		$this->context->assemble(AssemblerType::Home);

		// The `learner_profile` query var holds the user's nicename;
		// resolve the learner and label the leaf with their full name.
		$learner = Sensei_Learner::find_by_query_var(get_query_var('learner_profile'));

		// Note that there's a `Sensei_Learner::get_full_name()` method,
		// which will show the learner's full name. But I fundamentally
		// disagree with overriding a user's choice of display name. So
		// we're using the built-in `User` crumb type.
		if ($learner instanceof WP_User) {
			$this->context->addCrumb(CrumbType::User, [
				'user' => $learner
			]);
		}

		$this->context->assemble(AssemblerType::Paged);
	}
}
