<?php

/**
 * Markup type enum.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup;

/**
 * The canonical built-in markup formats — the source of truth mapping each key
 * to its class via `className()`. `MarkupServiceProvider` tags each class under
 * `Markup::TAG` from these cases, so the resolver can build a format by key and
 * enumerate the available formats (including third-party additions).
 */
enum MarkupType: string implements MarkupDefinition
{
	case Html           = 'html';
	case Microdata      = 'microdata';
	case Rdfa           = 'rdfa';
	case JsonLinkedData = 'json-ld';

	/**
	 * @inheritDoc
	 */
	public function className(): string
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return match ($this) {
			self::Html           => Type\Html::class,
			self::Microdata      => Type\Microdata::class,
			self::Rdfa           => Type\Rdfa::class,
			self::JsonLinkedData => Type\JsonLinkedData::class
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
		return 'x3p0/breadcrumbs/markup/' . $this->value;
	}
}
