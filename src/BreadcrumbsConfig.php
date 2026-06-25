<?php

/**
 * Breadcrumbs configuration.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

/**
 * Immutable configuration object passed through the breadcrumbs pipeline. It
 * controls how the trail is built: the labels used for generated crumbs, which
 * taxonomy represents a given post type, whether a post type's permalink
 * rewrite tags are mapped into crumbs, and whether the network (multisite)
 * crumb is shown.
 */
final class BreadcrumbsConfig
{
	/**
	 * Translated labels for generated crumbs, keyed by label slug. Caller
	 * values are merged over the built-in defaults.
	 *
	 * @var array<string, string>
	 */
	private readonly array $labels;

	/**
	 * Whether each post type's permalink rewrite tags map into crumbs, keyed
	 * by post type. Caller values are merged over the built-in defaults.
	 *
	 * @var array<string, bool>
	 */
	private readonly array $mapRewriteTags;

	/**
	 * Stores the config values, merging the given labels and rewrite-tag
	 * settings on top of the built-in defaults so callers only need to
	 * override what differs.
	 */
	public function __construct(
		array                  $labels         = [],
		array                  $mapRewriteTags = [],
		private readonly array $postTaxonomy   = [],
		private readonly bool  $network        = false
	) {
		$this->labels         = array_merge($this->defaultLabels(), $labels);
		$this->mapRewriteTags = array_merge($this->defaultRewriteTags(), $mapRewriteTags);
	}

	/**
	 * Builds a config from an associative array, ignoring any keys that do
	 * not map to a constructor parameter.
	 */
	public static function fromArray(array $options = []): self
	{
		return new self(...array_intersect_key($options, array_flip([
			'labels',
			'mapRewriteTags',
			'postTaxonomy',
			'network'
		])));
	}

	/**
	 * Returns the label registered under the given key, or an empty string
	 * if none is set.
	 */
	public function getLabel(string $key): string
	{
		return $this->labels[$key] ?? '';
	}

	/**
	 * Returns the taxonomy mapped to the given post type for building its
	 * crumbs, or an empty string if none is mapped.
	 */
	public function getPostTaxonomy(string $postType): string
	{
		return $this->postTaxonomy[$postType] ?? '';
	}

	/**
	 * Determines whether the given post type's permalink rewrite tags should
	 * be mapped into crumbs. Defaults to `true` for post types without an
	 * explicit setting.
	 */
	public function mapRewriteTags(string $postType): bool
	{
		return $this->mapRewriteTags[$postType] ?? true;
	}

	/**
	 * Determines whether to show the network crumb for multisite installs.
	 */
	public function showNetwork(): bool
	{
		return $this->network;
	}

	/**
	 * Returns the built-in, translated labels used for generated crumbs,
	 * keyed by label slug. Caller-supplied labels are merged on top of these.
	 */
	private function defaultLabels(): array
	{
		// phpcs:disable Generic.Functions.FunctionCallArgumentSpacing.TooMuchSpaceAfterComma
		return [
			'home'                => __('Home', 'x3p0-breadcrumbs'),
			'untitled'            => __('Untitled', 'x3p0-breadcrumbs'),
			'error_404'           => __('Page not found', 'x3p0-breadcrumbs'),
			'archives'            => __('Archives', 'x3p0-breadcrumbs'),
			// Translators: %s is the search query.
			'search'              => __('Search results for: %s', 'x3p0-breadcrumbs'),
			// Translators: %s is the page number.
			'paged'               => __('Page %s', 'x3p0-breadcrumbs'),
			// Translators: %s is the page number.
			'paged_comments'      => __('Comment Page %s', 'x3p0-breadcrumbs'),
			// Translators: Hour archive title. %s is the hour time format.
			'archive_hour'        => __('Hour %s', 'x3p0-breadcrumbs'),
			// Translators: Minute archive title. %s is the minute time format.
			'archive_minute'      => __('Minute %s', 'x3p0-breadcrumbs'),
			// Translators: Second archive title. %s is the second time format.
			'archive_second'      => __('Second %s', 'x3p0-breadcrumbs'),
			// Translators: Weekly archive title. %s is the week date format.
			'archive_week'        => __('Week %s', 'x3p0-breadcrumbs'),

			// "%s" is replaced with the translated date/time format.
			'archive_day'         => '%s',
			'archive_month'       => '%s',
			'archive_year'        => '%s'
		];
		// phpcs:enable
	}

	/**
	 * Returns an array of the default post rewrite tag settings. Array keys
	 * should be the post type and array values a boolean that sets whether
	 * the rewrite tags should be mapped for the permalink structure as
	 * breadcrumbs.
	 */
	private function defaultRewriteTags(): array
	{
		$types = array_filter(
			get_post_types(['publicly_queryable' => true], 'objects'),
			fn($type) => is_array($type->rewrite) && str_contains($type->rewrite['slug'] ?? '', '%')
		);

		return ['post' => true] + array_fill_keys(array_keys($types), true);
	}
}
