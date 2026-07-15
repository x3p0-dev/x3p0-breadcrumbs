<?php

/**
 * Author crumb class.
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
 * Crumb for an author archive. Labels with the user's display name and links
 * to their posts archive URL.
 */
final class Author extends Crumb
{
	/**
	 * @inheritDoc
	 */
	public function __construct(
		BreadcrumbsContext $context,
		#[NoAutowire] public readonly WP_User $user
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
		return get_author_posts_url($this->user->ID);
	}
}
