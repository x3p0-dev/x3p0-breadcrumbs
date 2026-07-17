<?php

/**
 * Markup key contract.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup;

use X3P0\Breadcrumbs\Contracts\TypeKey;

/**
 * Marks a `TypeKey` as naming a markup type, so markup-facing APIs can accept a
 * typed reference — the `MarkupType` enum or a third-party implementation —
 * without widening to every subsystem's keys. Adds nothing beyond the inherited
 * `key()`; it exists to keep the markup type distinct from crumb, query, and
 * assembler keys at the type level.
 */
interface MarkupKey extends TypeKey
{
}
