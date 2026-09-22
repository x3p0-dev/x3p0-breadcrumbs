<?php

/**
 * Icon option registrar class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Icon;

use X3P0\Breadcrumbs\Icon\Event\IconOptionsRegistered;
use X3P0\Breadcrumbs\Packages\Event\Dispatcher;
use X3P0\Breadcrumbs\Packages\Framework\Contracts\Bootable;

/**
 * Seeds the `IconOptionGroupRegistry` and `IconOptionRegistry` with the
 * built-in groups and options — each declared by its own
 * {@see IconOptionGroupKey} or {@see IconOptionKey} case — plus one option per
 * viewable post type and public taxonomy. Runs very late on `init` so every
 * post type and taxonomy is registered first, then dispatches
 * `IconOptionsRegistering` for third-party code to add to or amend the result.
 */
final class IconOptionRegistrar implements Bootable
{
	/**
	 * @var  Icon
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const POST_TYPE_ICON = Icon::Article;

	/**
	 * @var  Icon
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const POST_TYPE_ARCHIVE_ICON = Icon::Archive;

	/**
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const TAXONOMY_ICON = 'core/tag';

	/**
	 * Core post types worth more than the generic icon above, by name.
	 * Consulted as each type is enumerated.
	 *
	 * @var  array<string, Icon|string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const CORE_POST_TYPE_ICONS = [
		'post'       => 'core/pencil',
		'attachment' => 'core/file'
	];

	/**
	 * Core taxonomies worth more than the generic taxonomy icon, by name.
	 *
	 * @var  array<string, Icon|string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const CORE_TAXONOMY_ICONS = [
		'category'    => 'core/category',
		'post_format' => Icon::Category
	];

	/**
	 * Stores the registries seeded here and the dispatcher the
	 * `IconOptionsRegistering` event is announced on.
	 */
	public function __construct(
		private readonly Dispatcher              $events,
		private readonly IconOptionRegistry      $options,
		private readonly IconOptionGroupRegistry $groups
	) {}

	/**
	 * @inheritDoc
	 */
	public function boot(): void
	{
		// Very late, so every post type and taxonomy is registered first.
		add_action('init', $this->register(...), PHP_INT_MAX);
	}

	/**
	 * Seeds the built-in groups and options, then opens the registries to
	 * listeners and bridges the event to WordPress.
	 */
	private function register(): void
	{
		$this->registerGroups();
		$this->registerBuiltInOptions();
		$this->registerPostTypeOptions();
		$this->registerTaxonomyOptions();

		$this->events->dispatch(
			new IconOptionsRegistered($this->options, $this->groups)
		)->broadcast();
	}

	/**
	 * Registers the built-in groups in declaration order.
	 */
	private function registerGroups(): void
	{
		foreach (IconOptionGroupKey::cases() as $key) {
			$this->groups->register($key, $key->group());
		}
	}

	/**
	 * Registers the option each {@see IconOptionKey} case ships with.
	 */
	private function registerBuiltInOptions(): void
	{
		foreach (IconOptionKey::cases() as $key) {
			$this->options->register($key, $key->option());
		}
	}

	/**
	 * Registers a labeled option per viewable post type's single-post crumb
	 * and, for post types with an archive, one per archive crumb. Labels come
	 * off the post type object, which WordPress always fills. Attachments are
	 * listed with the media options rather than the post types.
	 */
	private function registerPostTypeOptions(): void
	{
		$postTypes = array_filter(
			get_post_types([], 'objects'),
			is_post_type_viewable(...)
		);

		foreach ($postTypes as $type) {
			$this->options->register(
				IconOptionKey::postType($type->name),
				new IconOption(
					self::CORE_POST_TYPE_ICONS[$type->name] ?? self::POST_TYPE_ICON,
					$type->labels->singular_name,
					'attachment' === $type->name ? IconOptionGroupKey::Media : IconOptionGroupKey::PostType,
					$type->name
				)
			);

			if ($type->has_archive) {
				$this->options->register(
					IconOptionKey::postTypeArchive($type->name),
					new IconOption(
						self::POST_TYPE_ARCHIVE_ICON,
						$type->labels->archives,
						IconOptionGroupKey::PostTypeArchive,
						$type->name
					)
				);
			}
		}
	}

	/**
	 * Registers a labeled option per public taxonomy's term crumb, taking
	 * its label from the taxonomy object the same way as the post type loop.
	 */
	private function registerTaxonomyOptions(): void
	{
		$taxonomies = array_filter(
			get_taxonomies([], 'objects'),
			is_taxonomy_viewable(...)
		);

		foreach ($taxonomies as $taxonomy) {
			$this->options->register(
				IconOptionKey::taxonomy($taxonomy->name),
				new IconOption(
					self::CORE_TAXONOMY_ICONS[$taxonomy->name] ?? self::TAXONOMY_ICON,
					$taxonomy->labels->singular_name,
					IconOptionGroupKey::Taxonomy,
					$taxonomy->name
				)
			);
		}
	}
}
