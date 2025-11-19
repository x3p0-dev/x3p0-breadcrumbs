<?php

/**
 * Post type crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Type;

use WP_Post_Type;
use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Crumb\AbstractCrumb;

final class PostType extends AbstractCrumb
{
	/**
	 * @inheritDoc
	 */
	public function __construct(
		BreadcrumbsContext $context,
		public readonly WP_Post_Type $postType
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function getLabel(): string
	{
		if (is_post_type_archive($this->postType->name)) {
			return post_type_archive_title('', false);
		}

		return $this->postType->labels->archives;
	}

	/**
	 * @inheritDoc
	 */
	public function getUrl(): string
	{
		return get_post_type_archive_link($this->postType->name);
	}
}
