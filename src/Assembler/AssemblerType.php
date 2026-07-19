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
 * The canonical built-in assembler types — the source of truth mapping each key
 * to its class via `className()`. `AssemblerServiceProvider` registers each
 * value as a container alias for that class, so a caller may pass the case, its
 * string key, or the class name to `BreadcrumbsContext::assemble()`.
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

	/**
	 * Returns this case's container alias — its key namespaced under the
	 * subsystem as `x3p0/breadcrumbs/{TYPE}/{key}` — so the same short key can
	 * be reused across subsystems without colliding in the container's single,
	 * global alias table.
	 */
	public function alias(): string
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return 'x3p0/breadcrumbs/assembler/' . $this->value;
	}
}
