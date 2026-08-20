<?php

/**
 * Term crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Type;

use WP_Term;
use X3P0\Breadcrumbs\Crumb\Crumb;
use X3P0\Breadcrumbs\Crumb\CrumbContext;
use X3P0\Breadcrumbs\Meta\MetaKey;
use X3P0\Breadcrumbs\Icon\IconOption;
use X3P0\Breadcrumbs\Packages\Framework\Container\Attributes\NoAutowire;

/**
 * Crumb representing a single taxonomy term (category, tag, or custom
 * taxonomy term). Its label is the term name and its URL is the term archive
 * link.
 */
final class Term extends Crumb
{
	/**
	 * @inheritDoc
	 */
	public function __construct(
		CrumbContext $context,
		#[NoAutowire] public readonly WP_Term $term
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function getSlug(): string
	{
		return 'term';
	}

	/**
	 * @inheritDoc
	 */
	public function getLabel(): string
	{
		$tax    = $this->term->taxonomy;
		$termId = $this->term->term_id;

		if (
			is_tax($tax, $termId)
			|| is_category($termId)
			|| is_tag($termId)
		) {
			return single_term_title('', false);
		}

		return $this->term->name;
	}

	/**
	 * @inheritDoc
	 */
	public function getUrl(): string
	{
		$link = get_term_link($this->term, $this->term->taxonomy);

		// `get_term_link()` returns a `WP_Error` for an invalid term;
		// fall back to an empty string.
		return is_wp_error($link) ? '' : $link;
	}

	/**
	 * Resolves this term's icon option per taxonomy.
	 *
	 * @inheritDoc
	 */
	public function iconKey(): string
	{
		return IconOption::taxonomyKey($this->term->taxonomy);
	}

	/**
	 * Returns this term's own icon override (term meta) when one is set —
	 * the site owner's explicit editorial choice — otherwise the taxonomy's
	 * icon option resolved by the parent.
	 *
	 * @inheritDoc
	 */
	public function getIcon(): string
	{
		$icon = get_term_meta($this->term->term_id, MetaKey::Icon->value, true);

		return '' !== $icon ? $icon : parent::getIcon();
	}
}
