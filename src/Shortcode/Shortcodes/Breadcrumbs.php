<?php

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Shortcode\Shortcodes;

use X3P0\Breadcrumbs\Shortcode\Shortcode;

final class Breadcrumbs implements Shortcode
{
	private const BLOCK_NAME = 'x3p0/breadcrumbs';

	public function render(
		array   $attrs     = [],
		?string $content   = null,
		string  $shortcode = ''
	): string {
		return render_block([
			'blockName' => self::BLOCK_NAME,
			'attrs'     => shortcode_atts([], $attrs, $shortcode)
		]);
	}
}
