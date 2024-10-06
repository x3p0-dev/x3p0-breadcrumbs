<?php

/**
 * Breadcrumbs class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2023, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

namespace X3P0\Breadcrumbs;

use X3P0\Breadcrumbs\Contracts;

class Breadcrumbs implements Contracts\Breadcrumbs
{
	/**
	 * @var Contracts\Crumb[]
	 */
	protected array $crumbs = [];

	/**
	 * Creates a new breadcrumbs object.
	 */
	public function __construct(
		protected Contracts\Environment $environment,
		protected array $options
	) {
		$this->options = wp_parse_args($this->options, [
			'labels'             => [],
			'post_taxonomy'      => [],
			'network'            => false,
			'post_rewrite_tags'  => true,
			'show_home_label'    => true
		]);

		$this->options['labels'] = wp_parse_args(
			$this->options['labels'],
			$this->defaultLabels()
		);

		$this->options['post_taxonomy'] = wp_parse_args(
			$this->options['post_taxonomy'],
			$this->defaultPostTaxonomies()
		);

		$this->options = apply_filters(
			'x3p0/breadcrumbs/config',
			$this->options
		);
	}

	public function environment(): Contracts\Environment
	{
		return $this->environment;
	}

	/**
	 * @return Contracts\Crumb[]
	 */
	public function all(): array
	{
		return [] === $this->crumbs ? $this->make()->crumbs : $this->crumbs;
	}

	/**
	 * Runs through a series of conditionals based on the current WordPress
	 * query. Once we figure out which page we're viewing, we create a new
	 * `Query` object and let it build the breadcrumbs.
	 */
	public function make(): Contracts\Breadcrumbs
	{
		$conditionals = [
			'is_404'               => 'error-404',
			'is_search'            => 'search',
			'is_front_page'        => 'front-page',
			'is_home'              => 'home',
			'is_singular'          => 'singular',
			'is_archive'           => 'archive',
		];

		foreach ($conditionals as $tag => $type) {
			if (call_user_func($tag)) {
				$this->query($type);
				return $this;
			}
		}

		// Return the object for chaining methods.
		return $this;
	}

	/**
	 * Creates a new `Query` object and runs its `make()` method.
	 */
	public function query(string $type, array $data = []): void
	{
		$query = $this->environment->getQuery($type);
		(new $query($this, ...$data))->make();
	}

	/**
	 * Creates a new `Builder` object and runs its `make()` method.
	 */
	public function build(string $type, array $data = []): void
	{
		$builder = $this->environment->getBuilder($type);
		(new $builder($this, ...$data))->make();
	}

	/**
	 * Creates a new `Crumb` object and adds it to the array of crumbs.
	 */
	public function crumb(string $type, array $data = []): void
	{
		$crumb = $this->environment->getCrumb($type);
		$this->crumbs[] = new $crumb($this, ...$data);
	}

	/**
	 * Returns a specific option or `null` if the option doesn't exist.
	 */
	public function option(string $name): mixed
	{
		return $this->options[$name] ?? null;
	}

	/**
	 * Returns a specific label or an empty string if it doesn't exist.
	 */
	public function label(string $name): string
	{
		$labels = $this->option('labels');
		return $labels[$name] ?? '';
	}

	/**
	 * Returns a specific post taxonomy or an empty string if one isn't set.
	 */
	public function postTaxonomy(string $post_type): string
	{
		$taxes = $this->option('post_taxonomy');
		return $taxes[$post_type] ?? '';
	}

	/**
	 * Returns an array of default labels.
	 */
	protected function defaultLabels(): array
	{
		// phpcs:disable Generic.Functions.FunctionCallArgumentSpacing.TooMuchSpaceAfterComma
		return [
			'title'               => __('Browse:',                               'x3p0-breadcrumbs'),
			'aria_label'          => _x('Breadcrumbs', 'breadcrumbs aria label', 'x3p0-breadcrumbs'),
			'home'                => __('Home',                                  'x3p0-breadcrumbs'),
			'error_404'           => __('404 Not Found',                         'x3p0-breadcrumbs'),
			'archives'            => __('Archives',                              'x3p0-breadcrumbs'),
			// Translators: %s is the search query.
			'search'              => __('Search results for: %s',                'x3p0-breadcrumbs'),
			// Translators: %s is the page number.
			'paged'               => __('Page %s',                               'x3p0-breadcrumbs'),
			// Translators: %s is the page number.
			'paged_comments'      => __('Comment Page %s',                       'x3p0-breadcrumbs'),
			// Translators: Minute archive title. %s is the minute time format.
			'archive_minute'      => __('Minute %s',                             'x3p0-breadcrumbs'),
			// Translators: Weekly archive title. %s is the week date format.
			'archive_week'        => __('Week %s',                               'x3p0-breadcrumbs'),

			// "%s" is replaced with the translated date/time format.
			'archive_minute_hour' => '%s',
			'archive_hour'        => '%s',
			'archive_day'         => '%s',
			'archive_month'       => '%s',
			'archive_year'        => '%s',
		];
		// phpcs:enable
	}

	/**
	 * Returns an array of default post taxonomies.
	 */
	protected function defaultPostTaxonomies(): array
	{
		$defaults = [];

		// If post permalink is set to `%postname%`, use the `category` taxonomy.
		if ('%postname%' === trim(get_option('permalink_structure'), '/')) {
			$defaults['post'] = 'category';
		}

		return $defaults;
	}
}
