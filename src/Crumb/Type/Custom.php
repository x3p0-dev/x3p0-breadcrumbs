<?php

/**
 * Custom crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Type;

use X3P0\Breadcrumbs\BreadcrumbsContext;
use X3P0\Breadcrumbs\Crumb\Crumb;

/**
 * An open-ended crumb built entirely from the values passed to it: a label and
 * an optional URL. Unlike the other crumb types, it derives nothing from the
 * request or a queried object, making it the general-purpose building block for
 * custom trail items — such as a plugin-provided endpoint — whose label and link
 * are known at build time. Omit the URL for a plain, unlinked crumb.
 */
final class Custom extends Crumb
{
	/**
	 * Stores the crumb's label and optional URL.
	 */
	public function __construct(
		BreadcrumbsContext $context,
		public readonly string $label,
		public readonly string $url = ''
	) {
		parent::__construct(context: $context);
	}

	/**
	 * @inheritDoc
	 */
	public function getLabel(): string
	{
		return $this->label;
	}

	/**
	 * @inheritDoc
	 */
	public function getUrl(): string
	{
		return $this->url;
	}
}
