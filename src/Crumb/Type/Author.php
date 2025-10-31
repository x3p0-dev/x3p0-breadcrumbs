<?php

/**
 * Author crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Type;

use WP_User;
use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Crumb\AbstractCrumb;

final class Author extends AbstractCrumb
{
	/**
	 * {@inheritdoc}
	 */
	public function __construct(
		protected BreadcrumbsContext $context,
		protected WP_User $user
	) {
		parent::__construct(...func_get_args());
	}

	/**
	 * {@inheritdoc}
	 */
	public function getLabel(): string
	{
		return get_the_author_meta('display_name', $this->user->ID);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getUrl(): string
	{
		return get_author_posts_url($this->user->ID);
	}
}
