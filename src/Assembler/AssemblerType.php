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
 * The canonical built-in assembler types — the source of truth mapping each
 * case to its class via `className()`. `AssemblerServiceProvider` tags each
 * class under `Assembler::TAG`, so a caller may pass the case or the class
 * name to `BreadcrumbsContext::assemble()`.
 */
enum AssemblerType implements AssemblerDefinition
{
	case Date;
	case Home;
	case Paged;
	case Path;
	case Post;
	case PostAncestors;
	case PostHierarchy;
	case PostRewriteTags;
	case PostTerms;
	case PostType;
	case RewriteFront;
	case Term;
	case TermAncestors;

	/**
	 * @inheritDoc
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
