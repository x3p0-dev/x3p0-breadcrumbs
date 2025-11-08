<?php

/**
 * Block registration class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Shortcode;

use X3P0\Breadcrumbs\Contracts\Bootable;
use X3P0\Breadcrumbs\Core\Container;
use X3P0\Breadcrumbs\Shortcode\Shortcodes\Breadcrumbs;

final class ShortcodeRegistrar implements Bootable
{
	public function __construct(private readonly Container $container)
	{}

	/**
	 * @inheritDoc
	 */
	public function boot(): void
	{
		add_action('init', $this->register(...));
	}

	/**
	 * Registers the block with WordPress.
	 */
	public function register(): void
	{
		add_shortcode('x3p0-breadcrumbs', function (...$args) {
			return $this->container->get(Breadcrumbs::class)->render(...$args);
		});
	}
}
