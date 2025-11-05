<?php

/**
 * Assembler registrar class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler;

/**
 * Registers assembler classes with the registry.
 */
final class AssemblerRegistrar
{
	public const HOME              = 'home';
	public const PAGED             = 'paged';
	public const PATH              = 'path';
	public const POST              = 'post';
	public const POST_ANCESTORS    = 'post-ancestors';
	public const POST_HIERARCHY    = 'post-hierarchy';
	public const POST_REWRITE_TAGS = 'post-rewrite-tags';
	public const POST_TERMS        = 'post-terms';
	public const POST_TYPE         = 'post-type';
	public const REWRITE_FRONT     = 'rewrite-front';
	public const TERM              = 'term';
	public const TERM_ANCESTORS    = 'term-ancestors';

	/**
	 * An array of assembler keys and their associated classes, to be stored
	 * in the assembler registry.
	 */
	private static function getAssemblers(): array
	{
		return [
			self::HOME              => Type\Home::class,
			self::PAGED             => Type\Paged::class,
			self::PATH              => Type\Path::class,
			self::POST              => Type\Post::class,
			self::POST_ANCESTORS    => Type\PostAncestors::class,
			self::POST_HIERARCHY    => Type\PostHierarchy::class,
			self::POST_REWRITE_TAGS => Type\PostRewriteTags::class,
			self::POST_TERMS        => Type\PostTerms::class,
			self::POST_TYPE         => Type\PostType::class,
			self::REWRITE_FRONT     => Type\RewriteFront::class,
			self::TERM              => Type\Term::class,
			self::TERM_ANCESTORS    => Type\TermAncestors::class,
		];
	}

	/**
	 * Registers default assemblers with the registry.
	 */
	public static function register(AssemblerRegistry $assemblerRegistry): void
	{
		foreach (self::getAssemblers() as $key => $className) {
			if (! $assemblerRegistry->isRegistered($key)) {
				$assemblerRegistry->register($key, $className);
			}
		}
	}
}
