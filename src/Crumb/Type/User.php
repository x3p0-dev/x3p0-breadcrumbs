<?php

/**
 * User crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Type;

use WP_User;
use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Crumb\Crumb;
use X3P0\Breadcrumbs\Packages\Framework\Container\Attributes\NoAutowire;

/**
 * Crumb for a single WordPress user. Labels with the user's display name and
 * links to the URL passed in, if any. A user has no single canonical front-end
 * URL, so the caller supplies it — omit it for an unlinked crumb, such as a
 * trail's leaf. See `Author` for the author-posts-archive specialization.
 */
final class User extends Crumb
{
	/**
	 * @inheritDoc
	 */
	public function __construct(
		BreadcrumbsContext $context,
		#[NoAutowire] public readonly WP_User $user,
		public readonly string $url = ''
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function getLabel(): string
	{
		return get_the_author_meta('display_name', $this->user->ID);
	}

	/**
	 * @inheritDoc
	 */
	public function getUrl(): string
	{
		return $this->url;
	}
}
