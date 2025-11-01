<?php

/**
 * Breadcrumbs configuration.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

/**
 * Creates a config for passing into breadcrumbs objects that determines how
 * the crumbs collection is generated.
 */
final class BreadcrumbsConfig
{
	public function __construct(
		private array $mapRewriteTags = [],
		private array $postTaxonomy   = [],
		private array $labels         = [],
		private bool  $network        = false
	) {
		$this->labels = array_merge($this->defaultLabels(), $this->labels);
		$this->mapRewriteTags = array_merge($this->defaultRewriteTags(), $this->mapRewriteTags);
	}

	/**
	 * Static helper function for creating the config from an array.
	 */
	public static function fromArray(array $options = []): static
	{
		return new static(
			labels:         $options['labels']           ?? [],
			mapRewriteTags: $options['map_rewrite_tags'] ?? [],
			postTaxonomy:   $options['post_taxonomy']    ?? [],
			network:        $options['network']          ?? false
		);
	}

	/**
	 * Gets a label.
	 */
	public function getLabel(string $name): string
	{
		return $this->labels[$name] ?? '';
	}

	/**
	 * Gets the taxonomy assigned to the post type.
	 */
	public function getPostTaxonomy(string $postType): string
	{
		return $this->postTaxonomy[$postType] ?? '';
	}

	/**
	 * Determines whether to map rewrite tags for a post type.
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
	 * Returns an array of default labels.
	 */
	protected function defaultLabels(): array
	{
		// phpcs:disable Generic.Functions.FunctionCallArgumentSpacing.TooMuchSpaceAfterComma
		return [
			'home'                => __('Home', 'x3p0-breadcrumbs'),
			'untitled'            => __('Untitled', 'x3p0-breadcrumbs'),
			'error_404'           => __('404 Not Found', 'x3p0-breadcrumbs'),
			'archives'            => __('Archives', 'x3p0-breadcrumbs'),
			// Translators: %s is the search query.
			'search'              => __('Search results for: %s', 'x3p0-breadcrumbs'),
			// Translators: %s is the page number.
			'paged'               => __('Page %s', 'x3p0-breadcrumbs'),
			// Translators: %s is the page number.
			'paged_comments'      => __('Comment Page %s', 'x3p0-breadcrumbs'),
			// Translators: Minute archive title. %s is the minute time format.
			'archive_minute'      => __('Minute %s', 'x3p0-breadcrumbs'),
			// Translators: Weekly archive title. %s is the week date format.
			'archive_week'        => __('Week %s', 'x3p0-breadcrumbs'),

			// "%s" is replaced with the translated date/time format.
			'archive_minute_hour' => '%s',
			'archive_hour'        => '%s',
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
	protected function defaultRewriteTags(): array
	{
		$types = array_filter(
			get_post_types(['publicly_queryable' => true], 'objects'),
			fn($type) => is_array($type->rewrite) && str_contains($type->rewrite['slug'] ?? '', '%')
		);

		return ['post' => true] + array_fill_keys(array_keys($types), true);
	}
}
