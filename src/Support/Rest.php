<?php

/**
 * REST API support class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Support;

use X3P0\Breadcrumbs\Contracts\Bootable;

class Rest implements Bootable
{

	/**
	 * @inheritDoc
	 */
	public function boot(): void
	{
		add_action('rest_api_init', [$this, 'register']);
	}

	/**
	 * Registers custom REST fields for use in the editor.
	 */
	public function register(): void
	{
		register_rest_field(
			'type',
			'x3p0-breadcrumbs/rewrite',
			[
				'get_callback' => function (array $data) {
					if ('post' === $data['slug']) {
						return [
							'slug' => get_option('permalink_structure')
						];
					}

					// Get the post type object.
					$type = get_post_type_object($data['slug']);

					// Return the rewrite property if it exists
					return $type->rewrite && is_array($type->rewrite)
						? $type->rewrite
						: null;
				},
				'schema' => [
					'description' => __('Post type rewrite configuration.', 'x3p0-breadcrumbs'),
					'type'        => [ 'object', 'null' ],
					'context'     => [ 'view', 'edit', 'embed' ],
					'readonly'    => true
				]
			]
		);
	}
}
