<?php

/**
 * Assembler key contract.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Assembler;

use X3P0\Breadcrumbs\Contracts\TypeKey;

/**
 * Marks a `TypeKey` as naming an assembler type, so assembler-facing APIs can
 * accept a typed reference — the `AssemblerType` enum or a third-party
 * implementation — without widening to every subsystem's keys. Adds nothing
 * beyond the inherited `key()`; it exists to keep the assembler type distinct
 * from crumb, query, and markup keys at the type level.
 */
interface AssemblerKey extends TypeKey
{
}
