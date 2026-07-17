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

use X3P0\Breadcrumbs\Contracts\ClassEnum;
use X3P0\Breadcrumbs\Support\ProvidesTypeKey;

/**
 * Defines the canonical string keys for the plugin's built-in assemblers. The
 * registrar uses these cases to seed the registry, and the values are the keys
 * that callers pass to the factory and to `BreadcrumbsContext::assemble()`.
 */
enum AssemblerType: string implements ClassEnum, AssemblerKey
{
	use ProvidesTypeKey;

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
	 * Returns the assembler class associated with the type, mapping each
	 * case to a concrete class under the `Type` sub-namespace.
	 *
	 * @return class-string<Assembler>
	 */
	public function className(): string
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return match ($this) {
			self::Date            => Type\Date::class,
			self::Home            => Type\Home::class,
			self::Paged           => Type\Paged::class,
			self::Path            => Type\Path::class,
			self::Post            => Type\Post::class,
			self::PostAncestors   => Type\PostAncestors::class,
			self::PostHierarchy   => Type\PostHierarchy::class,
			self::PostRewriteTags => Type\PostRewriteTags::class,
			self::PostTerms       => Type\PostTerms::class,
			self::PostType        => Type\PostType::class,
			self::RewriteFront    => Type\RewriteFront::class,
			self::Term            => Type\Term::class,
			self::TermAncestors   => Type\TermAncestors::class
		};
	}
}
