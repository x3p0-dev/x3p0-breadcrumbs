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

/**
 * Implements the `Breadcrumbs` contract using using query classes to generate
 * an array of breadcrumbs that can then be used to output a breadcrumb trail.
 */
class Breadcrumbs implements Contracts\Breadcrumbs
{
	/**
	 * @var Contracts\Crumb[]
	 */
	protected array $crumbs = [];

	/**
	 * Creates a new breadcrumbs object. The constructor requires an
	 * `Environment` implementation and an optional array of arguments for
	 * configuring the generated breadcrumbs.
	 */
	public function __construct(
		protected Contracts\Environment $environment,
		protected array $options = []
	) {
		$this->options = wp_parse_args($this->options, [
			'labels'           => [],
			'map_rewrite_tags' => [],
			'post_taxonomy'    => [],
			'network'          => false,
			'show_home_label'  => true
		]);

		$this->options['labels'] = wp_parse_args(
			$this->options['labels'],
			$this->defaultLabels()
		);

		$this->options['map_rewrite_tags'] = wp_parse_args(
			$this->options['map_rewrite_tags'],
			$this->defaultRewriteTags()
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

	/**
	 * Runs through a series of conditionals based on the current WordPress
	 * query. Once we figure out which page we're viewing, we create a new
	 * `Query` object and let it build the breadcrumbs.
	 */
	public function make(): Contracts\Breadcrumbs
	{
		$conditionals = [
			'is_404'        => 'error-404',
			'is_search'     => 'search',
			'is_front_page' => 'front-page',
			'is_home'       => 'home',
			'is_singular'   => 'singular',
			'is_archive'    => 'archive'
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
	 * {@inheritdoc}
	 */
	public function getCrumbs(): array
	{
		return [] === $this->crumbs ? $this->make()->crumbs : $this->crumbs;
	}

	/**
	 * {@inheritdoc}
	 */
	public function query(string $name, array $params = []): void
	{
		$query = $this->environment->queries()->get(
			$name,
			$params + [ 'breadcrumbs' => $this ]
		);

		$query && $query->make();
	}

	/**
	 * {@inheritdoc}
	 */
	public function build(string $name, array $params = []): void
	{
		$builder = $this->environment->builders()->get(
			$name,
			$params + [ 'breadcrumbs' => $this ]
		);

		$builder && $builder->make();
	}

	/**
	 * {@inheritdoc}
	 */
	public function crumb(string $name, array $params = []): void
	{
		if ($this->environment->crumbs()->has($name)) {
			$this->crumbs[] = $this->environment->crumbs()->get(
				$name,
				$params + [ 'breadcrumbs' => $this ]
			);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function option(string $name): mixed
	{
		return $this->options[$name] ?? null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function label(string $name): string
	{
		$labels = $this->option('labels');
		return $labels[$name] ?? '';
	}

	/**
	 * {@inheritdoc}
	 */
	public function mapRewriteTags(string $post_type): bool
	{
		$mappings = $this->option('map_rewrite_tags');
		return $mappings[$post_type] ?? true;
	}

	/**
	 * {@inheritdoc}
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
	 * Returns an array of the default post rewrite tag settings. Array keys
	 * should be the post type and array values a boolean that sets whether
	 * the rewrite tags should be mapped for the permalink structure as
	 * breadcrumbs.
	 */
	protected function defaultRewriteTags(): array
	{
		return [ 'post' => true ];
	}

	/**
	 * Returns an array of default post taxonomies. By default, we're only
	 * concerned with the Core `post` post type. If its permalink is set to
	 * `%postname%`, use the `category` taxonomy.
	 */
	protected function defaultPostTaxonomies(): array
	{
		$structure = trim(get_option('permalink_structure'), '/');

		return '%postname%' === $structure ? [ 'post' => 'category' ] : [];
	}
}
