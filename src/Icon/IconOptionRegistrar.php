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
 * Seeds the `IconOptionRegistry` with the built-in groups and options: the
 * static options (home, date archives, search, …) plus one per viewable post
 * type and public taxonomy, enumerated from what's actually registered with
 * WordPress, each under the group it belongs to. Runs
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
		$this->registerGroups();
		$this->registerStaticOptions();
		$this->registerPostTypeOptions();
		$this->registerTaxonomyOptions();
		$this->setCoreObjectIcons();
		$this->registerMediaOptions();

		$this->events->dispatch(
			new IconOptionsRegistered($this->options)
		)->broadcast();
	}

	/**
	 * Registers the groups the block editor sorts its icon controls into,
	 * first and in listing order. The catch-all leads, since it holds the
	 * options a site owner reaches for most; the WordPress object families
	 * follow. An extension adding a group of its own on
	 * `IconOptionsRegistered` lands after all of these.
	 */
	private function registerGroups(): void
	{
		$this->options->addGroup(IconOption::GROUP_GENERAL, __('General', 'x3p0-breadcrumbs'));
		$this->options->addGroup(IconOption::GROUP_POST_TYPE, __('Post Types', 'x3p0-breadcrumbs'));
		$this->options->addGroup(IconOption::GROUP_POST_TYPE_ARCHIVE, __('Post Type Archives', 'x3p0-breadcrumbs'));
		$this->options->addGroup(IconOption::GROUP_TAXONOMY, __('Taxonomies', 'x3p0-breadcrumbs'));
		$this->options->addGroup(IconOption::GROUP_MEDIA, __('Media', 'x3p0-breadcrumbs'));
	}

	/**
	 * Registers the fixed, built-in options: one per crumb type that resolves
	 * a key of its own rather than one derived from a post type or taxonomy
	 * (those are enumerated below). Most are labeled, so the crumbs the plugin
	 * puts in a trail have a control behind them.
	 *
	 * The last five are registered without one. Every icon the plugin can put
	 * on screen is an option — nothing renders from a literal buried in a
	 * crumb — but a label is a promise that a site owner can act on the
	 * setting, and these are icons rather than settings:
	 *
	 * - `archive` is the general fallback for archive views with nothing more
	 *   specific to resolve; the views that do — dates, times, post types,
	 *   taxonomies — all have options of their own above and below.
	 * - `custom` backs a crumb that is whatever the code building it made it,
	 *   and that code passes its own icon, which outranks anything configured
	 *   here; a control could only reach the ones built without one.
	 * - `fallback` is the last resort in `Crumb::getIcon()`, reached by a
	 *   crumb whose own key nothing is registered under.
	 * - `privacy-policy` and `posts-page` each name one particular page, and a
	 *   page's icon belongs to that page's own icon meta. They carry the
	 *   default `Post::fallbackIcon()` resolves for those two pages.
	 *
	 * The `network-site` option is registered only on a multisite network,
	 * since nowhere else can a trail contain the crumb that resolves it. The
	 * network home crumb has no option at all: it *is* the home crumb when a
	 * network is running, so it resolves `home` and is configured there.
	 */
	private function registerStaticOptions(): void
	{
		$this->options->add(
			new IconOption('separator',       Icon::Chevron,     __('Separator', 'x3p0-breadcrumbs')),
			new IconOption('home',            'core/home',       __('Home', 'x3p0-breadcrumbs')),
			new IconOption('date',            'core/calendar',   __('Date archives', 'x3p0-breadcrumbs')),
			new IconOption('time',            'core/scheduled',  __('Time archives', 'x3p0-breadcrumbs')),
			new IconOption('user',            'core/people',     __('User', 'x3p0-breadcrumbs')),
			new IconOption('search',          'core/search',     __('Search', 'x3p0-breadcrumbs')),
			new IconOption('error-404',       'core/error',      __('Page not found', 'x3p0-breadcrumbs')),
			new IconOption('paged',           Icon::Description, __('Pagination', 'x3p0-breadcrumbs')),
			new IconOption('private-post',    Icon::Unseen,      __('Private', 'x3p0-breadcrumbs')),
			new IconOption('protected-post',  'core/key',        __('Password protected', 'x3p0-breadcrumbs')),
			// Registered without a label: every icon the plugin can render is
			// an option, but not every one is worth a block control.
			new IconOption('archive',        Icon::Archive),
			new IconOption('custom',         Icon::Article),
			new IconOption('fallback',       Icon::Article),
			new IconOption('privacy-policy', 'core/shield'),
			new IconOption('posts-page',     Icon::Archive)
		);

		if (is_multisite()) {
			$this->options->add(new IconOption(
				'network-site',
				'core/desktop',
				__('Network Site', 'x3p0-breadcrumbs')
			));
		}
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
	 * after that enumeration and goes through `update()`, so each keeps the
	 * label and slug derived from its object — the same call an extension
	 * makes on `IconOptionsRegistered`. Core objects not listed here are
	 * already served by the generic default.
	 */
	private function setCoreObjectIcons(): void
	{
		$this->options->update(IconOption::postTypeKey('post'),        icon: 'core/pencil');
		$this->options->update(IconOption::taxonomyKey('category'),    icon: 'core/category');
		$this->options->update(IconOption::taxonomyKey('post_format'), icon: Icon::Category);
	}

	/**
	 * Registers an option per kind of media an attachment crumb can tell apart
	 * — see `Crumb\Type\Post::iconOptionKey()`, which resolves them — and
	 * gathers them with the attachment post type's own option under the media
	 * group. What kind of file a piece of media is says more about it than the
	 * fact that WordPress stores it as an attachment, and being an image or a
	 * video is a state any attachment can be in rather than a fact about a
	 * particular one, so each is configurable for all of them at once.
	 *
	 * The attachment post type's option stays where it is and keeps the label
	 * WordPress derived for it; it is the catch-all for media that is none of
	 * these — a PDF, an archive — so it belongs in the group beside them.
	 * Runs after the post types are enumerated, since it amends one of them.
	 */
	private function registerMediaOptions(): void
	{
		$this->options->add(
			new IconOption('media-image', 'core/image',         __('Image', 'x3p0-breadcrumbs'), IconOption::GROUP_MEDIA),
			new IconOption('media-audio', 'core/audio',         __('Audio', 'x3p0-breadcrumbs'), IconOption::GROUP_MEDIA),
			new IconOption('media-video', 'core/capture-video', __('Video', 'x3p0-breadcrumbs'), IconOption::GROUP_MEDIA)
		);

		$this->options->update(
			IconOption::postTypeKey('attachment'),
			icon: 'core/file',
			group: IconOption::GROUP_MEDIA
		);
	}
}
