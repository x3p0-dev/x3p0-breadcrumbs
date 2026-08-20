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

use X3P0\Breadcrumbs\Packages\Framework\Contracts\Bootable;

/**
 * Seeds the `IconOptions` registry with the built-in options: the static ones
 * (home, date archives, search, …) plus one per viewable post type and public
 * taxonomy, enumerated from what's actually registered with WordPress. Runs
 * very late on `init` so every post type and taxonomy — core, theme, or
 * plugin — is already registered, and so the block editor can consume the
 * finished list instead of re-enumerating them client-side.
 *
 * Seeding fills blanks rather than overwriting: an option registered earlier
 * (by an extension or third-party code) keeps its label and icon, so
 * built-ins can be retargeted simply by registering the same key first.
 */
final class IconOptionRegistrar implements Bootable
{
	/**
	 * Default icon for a post type's single-post option when nothing more
	 * specific is registered for it.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const POST_TYPE_ICON = 'x3p0-breadcrumbs/article';

	/**
	 * Default icon for a post type's archive option when nothing more
	 * specific is registered for it.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const POST_TYPE_ARCHIVE_ICON = 'core/category';

	/**
	 * Default icon for a taxonomy's term option when nothing more specific
	 * is registered for it.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const TAXONOMY_ICON = 'core/tag';

	/**
	 * Labels claimed by options as they're registered. The block editor's
	 * panel component tracks its controls by label, so two same-labeled
	 * options (e.g. core's `post_tag` and WooCommerce's `product_tag`, both
	 * "Tag") would collide there; a claimed label falls through to the next
	 * candidate, then to a slug-qualified version (always unique).
	 *
	 * @var array<string, bool>
	 */
	private array $usedLabels = [];

	/**
	 * Stores the registry the built-in options are seeded into.
	 */
	public function __construct(private readonly IconOptions $options)
	{}

	/**
	 * @inheritDoc
	 */
	public function boot(): void
	{
		// Very late, so every post type and taxonomy is registered first.
		add_action('init', $this->register(...), PHP_INT_MAX);
	}

	/**
	 * Registers the built-in options: static ones first, then one per
	 * viewable post type (plus its archive, when it has one) and one per
	 * public taxonomy.
	 */
	private function register(): void
	{
		$this->registerStaticOptions();
		$this->registerPostTypeOptions();
		$this->registerTaxonomyOptions();
	}

	/**
	 * Registers the fixed, built-in options: the labeled ones offered as
	 * block controls and the unlabeled default-carriers that only supply a
	 * crumb type's default icon. Existing keys are skipped so anything
	 * registered earlier wins.
	 */
	private function registerStaticOptions(): void
	{
		$options = [
			new IconOption('separator', 'x3p0-breadcrumbs/chevron', __('Separator', 'x3p0-breadcrumbs')),
			new IconOption('home', 'core/home', __('Home', 'x3p0-breadcrumbs')),
			new IconOption('date', 'core/calendar', __('Date archives', 'x3p0-breadcrumbs')),
			new IconOption('time', 'core/scheduled', __('Time archives', 'x3p0-breadcrumbs')),
			new IconOption('author', 'core/people', __('Author', 'x3p0-breadcrumbs')),
			new IconOption('search', 'core/search', __('Search', 'x3p0-breadcrumbs')),
			new IconOption('error-404', 'core/error', __('Page not found', 'x3p0-breadcrumbs')),

			// Unlabeled default-carriers: resolvable, no block control.
			// The `archive` icon is a placeholder pending a purpose-picked
			// one; it deliberately has no label for now, since archive
			// views are covered by the date/time/post type/taxonomy
			// options above and below.
			new IconOption('archive', 'core/calendar'),
			new IconOption('paged', 'x3p0-breadcrumbs/description'),
			new IconOption('paged-comments', 'x3p0-breadcrumbs/description'),
			new IconOption('paged-singular', 'x3p0-breadcrumbs/description'),
			new IconOption('paged-query-block', 'x3p0-breadcrumbs/description'),
			new IconOption('network', 'core/home'),
			new IconOption('network-site', 'core/desktop'),
			new IconOption('user', 'core/people'),
			new IconOption(IconOption::postTypeKey('post'), 'core/pencil'),
			new IconOption(IconOption::postTypeKey('page'), self::POST_TYPE_ICON),
			new IconOption(IconOption::postTypeKey('attachment'), 'core/file'),
			new IconOption(IconOption::taxonomyKey('category'), 'core/category'),
			new IconOption(IconOption::taxonomyKey('post_tag'), self::TAXONOMY_ICON)
		];

		foreach ($options as $option) {
			if (! $this->options->has($option->key)) {
				$this->options->add($option);
			}

			$this->claimLabel($this->options->get($option->key)->label);
		}
	}

	/**
	 * Registers a labeled option per viewable post type's single-post crumb
	 * and, for post types with an archive, one per archive crumb. An earlier
	 * registration for the same key (a static seed above, or an extension's)
	 * keeps its icon — and its label, when it set one — so this loop only
	 * fills the blanks.
	 */
	private function registerPostTypeOptions(): void
	{
		$postTypes = array_filter(
			get_post_types([], 'objects'),
			'is_post_type_viewable'
		);

		foreach ($postTypes as $type) {
			$this->registerDynamicOption(
				IconOption::postTypeKey($type->name),
				[
					$type->labels->singular_name,
					$type->labels->template_name ?? ''
				],
				$type->name,
				self::POST_TYPE_ICON
			);

			if ($type->has_archive) {
				$this->registerDynamicOption(
					IconOption::postTypeArchiveKey($type->name),
					[
						$type->labels->archives,
						$type->labels->name
					],
					$type->name,
					self::POST_TYPE_ARCHIVE_ICON
				);
			}
		}
	}

	/**
	 * Registers a labeled option per public taxonomy's term crumb, filling
	 * blanks the same way as the post type loop.
	 */
	private function registerTaxonomyOptions(): void
	{
		$taxonomies = get_taxonomies(['publicly_queryable' => true], 'objects');

		foreach ($taxonomies as $taxonomy) {
			$this->registerDynamicOption(
				IconOption::taxonomyKey($taxonomy->name),
				[
					$taxonomy->labels->singular_name,
					$taxonomy->labels->name,
					$taxonomy->labels->template_name ?? ''
				],
				$taxonomy->name,
				self::TAXONOMY_ICON
			);
		}
	}

	/**
	 * Registers a dynamically-enumerated option, preserving an earlier
	 * registration's label and icon when set and supplying the given ones
	 * only where blank. Whichever label wins is claimed, so a later option
	 * can't collide with it.
	 *
	 * @param list<string> $labels Candidate labels, most preferred first.
	 */
	private function registerDynamicOption(string $key, array $labels, string $qualifier, string $icon): void
	{
		$existing = $this->options->get($key);

		$this->options->add(new IconOption(
			key:   $key,
			icon:  $existing?->icon ?: $icon,
			label: $existing?->label ?: $this->uniqueLabel($labels, $qualifier)
		));

		$this->claimLabel($this->options->get($key)->label);
	}

	/**
	 * Returns the first candidate label no other option has claimed yet,
	 * falling through to a slug-qualified version of the first candidate —
	 * always unique — when every candidate is taken. The common case never
	 * shows the slug at all; only an actual collision does (e.g. core's
	 * `post_tag` and WooCommerce's `product_tag`, both "Tag", where the
	 * later one becomes "Tag (product_tag)").
	 *
	 * @param list<string> $candidates Candidate labels, most preferred first.
	 */
	private function uniqueLabel(array $candidates, string $qualifier): string
	{
		if ([] === $candidates = array_values(array_filter($candidates))) {
			return $qualifier;
		}

		foreach ($candidates as $candidate) {
			if (! isset($this->usedLabels[$candidate])) {
				return $candidate;
			}
		}

		return sprintf('%1$s (%2$s)', $candidates[0], $qualifier);
	}

	/**
	 * Marks a label as claimed so a later option can't reuse it. Empty
	 * labels (default-carriers) are ignored.
	 */
	private function claimLabel(string $label): void
	{
		if ('' !== $label) {
			$this->usedLabels[$label] = true;
		}
	}
}
