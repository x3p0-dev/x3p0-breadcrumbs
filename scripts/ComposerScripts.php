<?php

/**
 * Composer scripts.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-a-boy-in-the-wild
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Scripts;

use Composer\Script\Event;
use X3P0\Prelude\Compose;
use X3P0\Skills\Installer;

class ComposerScripts
{
	public static function devScripts(Event $event): void
	{
		if (! $event->isDevMode()) {
			return;
		}

		Compose::run($event);
		Installer::install($event);
	}
}
