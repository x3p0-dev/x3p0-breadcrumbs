<?php

/**
 * Icon preset registrar class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Icon;

use X3P0\Breadcrumbs\Icon\Event\IconPresetsRegistered;
use X3P0\Breadcrumbs\Packages\Event\Dispatcher;
use X3P0\Breadcrumbs\Packages\Framework\Contracts\Bootable;

/**
 * Registers every icon preset this plugin ships, then opens the registry to
 * third-party code by dispatching `IconPresetsRegistered`.
 *
 * Three sets go in. The keys the plugin owns are the cases of
 * {@see IconPresetKey}, each of which knows its own icon, label, and group.
 * The rest are derived from WordPress: one preset per viewable post type, one
 * per post type that has an archive, and one per viewable taxonomy, labeled
 * from the object itself so a control reads as whatever the site calls that
 * thing.
 *
 * All of it runs very late on `init`, so every post type and taxonomy is
 * registered first and a listener sees the finished set.
 */
final class IconPresetRegistrar implements Bootable
{
	/**
	 * Icons for the core post types worth more than the generic one.
	 *
	 * @var  array<string, Icon|string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const CORE_POST_TYPE_ICONS = [
		'post'       => 'core/pencil',
		'attachment' => 'core/file'
	];

	/**
	 * Icons for the core taxonomies worth more than the generic one.
	 *
	 * @var  array<string, Icon|string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const CORE_TAXONOMY_ICONS = [
		'category'    => 'core/category',
		'post_format' => Icon::Category
	];

	/**
	 * Stores the registry seeded here and the dispatcher the event is
	 * announced on.
	 */
	public function __construct(
		private readonly Dispatcher  $events,
		private readonly IconPresets $presets
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
	 * Seeds the plugin's presets, then opens the registry to listeners and
	 * bridges the event to WordPress.
	 */
	private function register(): void
	{
		$this->registerOwnKeys();
		$this->registerPostTypes();
		$this->registerTaxonomies();

		$this->events->dispatch(
			new IconPresetsRegistered($this->presets)
		)->broadcast();
	}

	/**
	 * Registers the preset each {@see IconPresetKey} case ships with.
	 */
	private function registerOwnKeys(): void
	{
		foreach (IconPresetKey::cases() as $key) {
			$this->presets->register($key, new IconPreset(
				$key->icon(),
				$key->label(),
				$key->group()
			));
		}
	}

	/**
	 * Registers a preset per viewable post type's single-post crumbs and,
	 * for post types with an archive, one per archive crumb. Labels come off
	 * the post type object, which WordPress always fills. Attachments are
	 * listed with the media controls rather than the post types.
	 */
	private function registerPostTypes(): void
	{
		$postTypes = array_filter(
			get_post_types([], 'objects'),
			is_post_type_viewable(...)
		);

		foreach ($postTypes as $type) {
			$this->presets->register(
				IconPresetKey::postType($type->name),
				new IconPreset(
					self::CORE_POST_TYPE_ICONS[$type->name] ?? Icon::Article,
					$type->labels->singular_name,
					'attachment' === $type->name ? IconGroup::Media : IconGroup::PostType,
					$type->name
				)
			);

			if ($type->has_archive) {
				$this->presets->register(
					IconPresetKey::postTypeArchive($type->name),
					new IconPreset(
						Icon::Archive,
						$type->labels->archives,
						IconGroup::PostTypeArchive,
						$type->name
					)
				);
			}
		}
	}

	/**
	 * Registers a preset per viewable taxonomy's term crumbs, taking its
	 * label from the taxonomy object the same way as the post types above.
	 */
	private function registerTaxonomies(): void
	{
		$taxonomies = array_filter(
			get_taxonomies([], 'objects'),
			is_taxonomy_viewable(...)
		);

		foreach ($taxonomies as $taxonomy) {
			$this->presets->register(
				IconPresetKey::taxonomy($taxonomy->name),
				new IconPreset(
					self::CORE_TAXONOMY_ICONS[$taxonomy->name] ?? 'core/tag',
					$taxonomy->labels->singular_name,
					IconGroup::Taxonomy,
					$taxonomy->name
				)
			);
		}
	}
}
