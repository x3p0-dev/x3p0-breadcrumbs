<?php

/**
 * Singular query class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

namespace X3P0\Breadcrumbs\Query;

use WP_Post;
use X3P0\Breadcrumbs\Contracts\Breadcrumbs;

class Singular extends Base
{
	/**
	 * {@inheritdoc}
	 */
	public function __construct(
		protected Breadcrumbs $breadcrumbs,
		protected ?WP_Post $post = null
	) {}

	/**
	 * {@inheritdoc}
	 */
	public function make(): void
	{
		$post = $this->post ?: get_queried_object();

		$this->breadcrumbs->build('home');
		$this->breadcrumbs->build('post', [ 'post' => $post ]);
		$this->breadcrumbs->build('paged');
	}
}
