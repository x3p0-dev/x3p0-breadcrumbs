<?php

/**
 * Sensei LMS quiz crumb.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Extension\SenseiLms\Crumb;

use X3P0\Breadcrumbs\BreadcrumbsConfig;
use X3P0\Breadcrumbs\Crumb\Crumb;

/**
 * Crumb representing a single quiz. Sensei copies the parent lesson's
 * `post_title` onto the quiz post whenever the lesson is saved
 * (`Sensei_Quiz::update_after_lesson_change()`), so the quiz's own title is
 * never distinct from the lesson crumb the trail already places before it.
 * This decorates the quiz's post crumb, keeping its URL but replacing the
 * label with the quiz post type's singular name.
 */
final class Quiz extends Crumb
{
	/**
	 * Wraps the crumb this decorates so the URL can fall back to it.
	 */
	public function __construct(
		BreadcrumbsConfig $config,
		private readonly Crumb $decoratedCrumb
	) {
		parent::__construct(config: $config);
	}

	/**
	 * @inheritDoc
	 */
	public function getLabel(): string
	{
		$postType = get_post_type_object('quiz');

		return $postType?->labels->singular_name ?: $this->decoratedCrumb->getLabel();
	}

	/**
	 * @inheritDoc
	 */
	public function getUrl(): string
	{
		return $this->decoratedCrumb->getUrl();
	}
}
