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
	/**
	 * An array of assembler keys and their associated classes, to be stored
	 * in the assembler registry.
	 */
	private const ASSEMBLERS = [
		'home'              => Type\Home::class,
		'paged'             => Type\Paged::class,
		'path'              => Type\Path::class,
		'post'              => Type\Post::class,
		'post-ancestors'    => Type\PostAncestors::class,
		'post-hierarchy'    => Type\PostHierarchy::class,
		'post-rewrite-tags' => Type\PostRewriteTags::class,
		'post-terms'        => Type\PostTerms::class,
		'post-type'         => Type\PostType::class,
		'rewrite-front'     => Type\RewriteFront::class,
		'term'              => Type\Term::class,
		'term-ancestors'    => Type\TermAncestors::class
	];

	/**
	 * Registers default assemblers with the registry.
	 */
	public static function register(AssemblerRegistry $assemblerRegistry): void
	{
		foreach (self::ASSEMBLERS as $key => $assemblerClass) {
			if (! $assemblerRegistry->isRegistered($key)) {
				$assemblerRegistry->register($key, $assemblerClass);
			}
		}
	}
}
