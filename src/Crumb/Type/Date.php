<?php

/**
 * Date crumb base class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Type;

use WP_Post;
use X3P0\Breadcrumbs\Crumb\Crumb;
use X3P0\Breadcrumbs\Crumb\CrumbContext;
use X3P0\Breadcrumbs\Icon\IconOptionKey;
use X3P0\Breadcrumbs\Packages\Framework\Container\Attributes\NoAutowire;

/**
 * Base for the date-based archive crumbs (`Day`, `Week`, `Month`, `Year`,
 * and — one level further specialized, for sub-day precision — the
 * `TimeArchive` types). Carries the `$post` a concrete type resolves its
 * date/time from and points every subclass at the shared `date` icon option,
 * so all date archives are configured with one setting.
 */
abstract class Date extends Crumb
{
	/**
	 * @inheritDoc
	 */
	public function __construct(
		CrumbContext $context,
		#[NoAutowire] public readonly ?WP_Post $post = null
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	protected function iconOptionKey(): IconOptionKey
	{
		return IconOptionKey::Date;
	}
}
