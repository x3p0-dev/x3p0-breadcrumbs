<?php

/**
 * Assembler type enum.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler;

/**
 * Defines the canonical string keys for the plugin's built-in assemblers. The
 * registrar uses these cases to seed the registry, and the values are the keys
 * that callers pass to the factory and to `BreadcrumbsContext::assemble()`.
 */
enum AssemblerType: string
{
	case Date            = 'date';
	case Home            = 'home';
	case Paged           = 'paged';
	case Path            = 'path';
	case Post            = 'post';
	case PostAncestors   = 'post-ancestors';
	case PostHierarchy   = 'post-hierarchy';
	case PostRewriteTags = 'post-rewrite-tags';
	case PostTerms       = 'post-terms';
	case PostType        = 'post-type';
	case RewriteFront    = 'rewrite-front';
	case Term            = 'term';
	case TermAncestors   = 'term-ancestors';

	/**
	 * Returns the assembler class associated with the type. Each case name
	 * matches a concrete class under the `Type` sub-namespace.
	 *
	 * @return class-string<Assembler>
	 */
	public function className(): string
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return __NAMESPACE__ . '\\Type\\' . $this->name;
	}
}
