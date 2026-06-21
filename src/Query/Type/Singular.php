<?php

/**
 * Singular query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Query\Type;

use WP_Post;
use X3P0\Breadcrumbs\Assembler\AssemblerType;
use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Query\Query;

final class Singular extends Query
{
	/**
	 * @inheritDoc
	 */
	public function __construct(
		BreadcrumbsContext $context,
		protected ?WP_Post $post = null
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function query(): void
	{
		$post = $this->post ?: get_queried_object();

		$this->context->assemble(AssemblerType::Home);
		$this->context->assemble(AssemblerType::Post, [ 'post' => $post ]);
		$this->context->assemble(AssemblerType::Paged);
	}
}
