<?php

/**
 * Crumb key contract.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb;

use X3P0\Breadcrumbs\Contracts\TypeKey;

/**
 * Marks a `TypeKey` as naming a crumb type, so crumb-facing APIs can accept a
 * typed reference — the `CrumbType` enum or a third-party implementation —
 * without widening to every subsystem's keys. Adds nothing beyond the inherited
 * `key()`; it exists to keep the crumb type distinct from query, assembler, and
 * markup keys at the type level.
 */
interface CrumbKey extends TypeKey
{
}
