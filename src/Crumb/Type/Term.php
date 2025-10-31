<?php

/**
 * Term crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025 Justin Tadlock
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Type;

use WP_Term;
use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Crumb\AbstractCrumb;

final class Term extends AbstractCrumb
{
	/**
	 * {@inheritdoc}
	 */
	public function __construct(
		protected BreadcrumbsContext $context,
		protected WP_Term $term
	) {
		parent::__construct(...func_get_args());
	}

	/**
	 * {@inheritdoc}
	 */
	public function getLabel(): string
	{
		$tax     = $this->term->taxonomy;
		$term_id = $this->term->term_id;

		if (
			is_tax($tax, $term_id)
			|| is_category($term_id)
			|| is_tag($term_id)
		) {
			return single_term_title('', false);
		}

		return $this->term->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getUrl(): string
	{
		return get_term_link($this->term, $this->term->taxonomy);
	}
}
