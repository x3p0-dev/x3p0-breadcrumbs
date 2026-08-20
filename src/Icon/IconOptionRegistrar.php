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
 * Seeds the `IconOptionRegistry` with the built-in options: the static ones
 * (home, date archives, search, …) plus one per viewable post type and public
 * taxonomy, enumerated from what's actually registered with WordPress. Runs
 * very late on `init` so every post type and taxonomy — core, theme, or
 * plugin — is already registered, and so the block editor can consume the
 * finished list instead of re-enumerating them client-side.
 *
 * Once seeded, the `IconOptionsRegistered` event hands the finished registry
 * to listeners. That event is the sole extension point: seeding overwrites, so
 * registering an option before this runs accomplishes nothing. A listener sees
 * the complete set and has the final say over it, with no hook order to reason
 * about.
 */
final class IconOptionRegistrar implements Bootable
{
	/**
	 * Icon every enumerated post type's single-post option gets, absent a
	 * better one from `setCoreObjectIcons()` or a listener.
	 *
	 * @var  Icon
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const POST_TYPE_ICON = Icon::Article;

	/**
	 * Icon every enumerated post type's archive option gets.
	 *
	 * @var  Icon
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const POST_TYPE_ARCHIVE_ICON = Icon::Archive;

	/**
	 * Icon every enumerated taxonomy's term option gets.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const TAXONOMY_ICON = 'core/tag';

	/**
	 * Stores the registry the built-in options are seeded into and the
	 * dispatcher the `IconOptionsRegistered` event is announced on once they
	 * are.
	 */
	public function __construct(
		private readonly Dispatcher $events,
		private readonly IconOptionRegistry $options
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
	 * Registers the built-in options: static ones first, then one per viewable
	 * post type (plus its archive, when it has one) and one per public
	 * taxonomy, then the handful of core-object icons worth more than the
	 * generic default. Announces the finished registry afterward so listeners
	 * can add or retarget options, then broadcasts the same event to
	 * WordPress so `add_action()` callbacks can do the same.
	 */
	private function register(): void
	{
		$this->registerStaticOptions();
		$this->registerPostTypeOptions();
		$this->registerTaxonomyOptions();
		$this->setCoreObjectIcons();

		$this->events->dispatch(
			new IconOptionsRegistered($this->options)
		)->broadcast();
	}

	/**
	 * Registers the fixed, built-in options: the labeled ones offered as
	 * block controls and the unlabeled default-carriers that only supply a
	 * crumb type's default icon. Nothing here corresponds to a post type or
	 * taxonomy — those are enumerated below.
	 */
	private function registerStaticOptions(): void
	{
		$this->options->add(
			new IconOption('separator', Icon::Chevron,    __('Separator', 'x3p0-breadcrumbs')),
			new IconOption('home',      'core/home',      __('Home', 'x3p0-breadcrumbs')),
			new IconOption('date',      'core/calendar',  __('Date archives', 'x3p0-breadcrumbs')),
			new IconOption('time',      'core/scheduled', __('Time archives', 'x3p0-breadcrumbs')),
			new IconOption('author',    'core/people',    __('Author', 'x3p0-breadcrumbs')),
			new IconOption('search',    'core/search',    __('Search', 'x3p0-breadcrumbs')),
			new IconOption('error-404', 'core/error',     __('Page not found', 'x3p0-breadcrumbs')),
			// Unlabeled default-carriers: resolvable, no block control.
			// The `archive` icon is a placeholder pending a purpose-picked
			// one; it deliberately has no label for now, since archive
			// views are covered by the date/time/post type/taxonomy
			// options above and below.
			new IconOption('archive',      'core/calendar'),
			new IconOption('network',      'core/home'),
			new IconOption('network-site', 'core/desktop'),
			new IconOption('paged',        Icon::Description),
			new IconOption('user',         'core/people')
		);
	}

	/**
	 * Registers a labeled option per viewable post type's single-post crumb
	 * and, for post types with an archive, one per archive crumb. Labels come
	 * straight off the post type object: WordPress fills `singular_name` and
	 * `archives` from its own defaults for every registered type, so neither
	 * can be empty. Two types may well declare the same label (core's
	 * `post_tag` and WooCommerce's `product_tag` are both "Tag"); each is
	 * registered as declared regardless, since a duplicate label is only a
	 * problem for the block editor's panel, which disambiguates its own
	 * controls (see `IconsPanel.js`).
	 */
	private function registerPostTypeOptions(): void
	{
		$postTypes = array_filter(
			get_post_types([], 'objects'),
			'is_post_type_viewable'
		);

		foreach ($postTypes as $type) {
			$this->options->add(IconOption::forPostType(
				$type->name,
				self::POST_TYPE_ICON,
				$type->labels->singular_name
			));

			if ($type->has_archive) {
				$this->options->add(IconOption::forPostTypeArchive(
					$type->name,
					self::POST_TYPE_ARCHIVE_ICON,
					$type->labels->archives
				));
			}
		}
	}

	/**
	 * Registers a labeled option per public taxonomy's term crumb, taking its
	 * label from the taxonomy object the same way as the post type loop.
	 */
	private function registerTaxonomyOptions(): void
	{
		$taxonomies = array_filter(
			get_taxonomies([], 'objects'),
			'is_taxonomy_viewable'
		);

		foreach ($taxonomies as $taxonomy) {
			$this->options->add(IconOption::forTaxonomy(
				$taxonomy->name,
				self::TAXONOMY_ICON,
				$taxonomy->labels->singular_name
			));
		}
	}

	/**
	 * Retargets the few core post types and taxonomies whose crumbs deserve
	 * something better than the generic icon the enumeration gave them. Runs
	 * after that enumeration and goes through `setIcon()`, so each keeps the
	 * label derived from its object — the same call an extension makes on
	 * `IconOptionsRegistered`. Core objects not listed here are already served
	 * by the generic default.
	 */
	private function setCoreObjectIcons(): void
	{
		$this->options->setIcon(IconOption::postTypeKey('post'),       'core/pencil');
		$this->options->setIcon(IconOption::postTypeKey('attachment'), 'core/file');
		$this->options->setIcon(IconOption::taxonomyKey('category'),   'core/category');
	}
}
