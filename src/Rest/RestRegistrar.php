<?php

/**
 * REST API registration.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Rest;

use X3P0\Breadcrumbs\Framework\Contracts\Bootable;

/**
 * Registers fields with the REST API needed for the block in the editor.
 */
final class RestRegistrar implements Bootable
{
	/**
	 * Defines the rewrite REST field attribute name.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const REWRITE_ATTRIBUTE = 'x3p0-breadcrumbs/rewrite';

	/**
	 * @inheritDoc
	 */
	public function boot(): void
	{
		add_action('rest_api_init', $this->register(...));
	}

	/**
	 * Registers custom REST fields for use in the editor.
	 */
	private function register(): void
	{
		register_rest_field('type', self::REWRITE_ATTRIBUTE, [
			'get_callback' => $this->getPostTypeRewrite(...),
			'schema' => [
				'description' => __('Post type rewrite configuration.', 'x3p0-breadcrumbs'),
				'type'        => [ 'object', 'null' ],
				'context'     => [ 'view', 'edit', 'embed' ],
				'readonly'    => true
			]
		]);
	}

	/**
	 * Returns rewrite data for `GET` responses. The `$data['slug']`
	 * property is expected to be a post type slug.
	 */
	private function getPostTypeRewrite(array $data): ?array
	{
		// The WordPress `post` post type's rewrite rules are defined in
		// the database and not as part of the post type registration.
		if ('post' === $data['slug']) {
			return [
				'slug' => get_option('permalink_structure')
			];
		}

		// Get the post type object.
		$type = get_post_type_object($data['slug']);

		// Return the rewrite property if it is an array.
		return $type->rewrite && is_array($type->rewrite)
			? $type->rewrite
			: null;
	}
}
