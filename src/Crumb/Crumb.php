<?php

/**
 * Crumb class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb;

use X3P0\Breadcrumbs\BreadcrumbsContext;

/**
 * Abstract base for a single item in the breadcrumb trail. A crumb is created
 * by a `Query` or `Assembler` and exposes everything needed to output the item:
 * a text label and, optionally, a URL. Concrete crumbs live under `Type` and
 * read shared config and factories from the supplied context. This class is the
 * contract that the rest of the system typehints against; subclasses override
 * `getLabel()` (and `getUrl()` where the crumb links somewhere).
 */
abstract class Crumb
{
	/**
	 * The crumb's type slug, assigned once by the factory to the registry
	 * key the crumb was built from. Left unset for crumbs built outside the
	 * factory, in which case `type()` derives a slug from the class name.
	 */
	private readonly string $type;

	/**
	 * Stores the shared breadcrumbs context, the entry point to config and
	 * the collection being built.
	 */
	public function __construct(protected readonly BreadcrumbsContext $context)
	{}

	/**
	 * Assigns the crumb's type slug. Called once by the factory with the
	 * registry key the crumb was built from; the `readonly` property makes
	 * it writable a single time.
	 */
	public function setType(string $type): void
	{
		$this->type = $type;
	}

	/**
	 * Returns the crumb's type slug, used for its `crumb--{type}` CSS class
	 * and to match it in the collection. This is the factory-assigned key
	 * when set, otherwise a kebab-cased form of the class short name
	 * (`PostType` becomes `post-type`) as a fallback for crumbs built
	 * outside the factory.
	 */
	public function getType(): string
	{
		return $this->type ?? strtolower(preg_replace(
			'/(?<!^)[A-Z]/',
			'-$0',
			basename(str_replace('\\', '/', static::class))
		));
	}

	/**
	 * Returns the internationalized text label shown for the crumb.
	 */
	abstract public function getLabel(): string;

	/**
	 * Returns the crumb URL, or an empty string when the crumb is not a link.
	 */
	public function getUrl(): string
	{
		return '';
	}
}
