<?php

/**
 * Builder class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Builder;

use TypeError;
use X3P0\Breadcrumbs\Contracts;

/**
 * Implements the `Builder` contract using query classes to generate an array of
 * breadcrumbs that can then be used to output a breadcrumb trail.
 */
class Builder implements Contracts\Builder
{
	/**
	 * Houses the array of `Contracts\Crumb` objects that make up the trail.
	 *
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
		$this->options = apply_filters(
			'x3p0/breadcrumbs/builder/config',
			array_replace_recursive([
				'labels'           => $this->defaultLabels(),
				'map_rewrite_tags' => $this->defaultRewriteTags(),
				'post_taxonomy'    => [],
				'network'          => false
			], $this->options)
		);
	}

	/**
	 * Runs through a series of conditionals based on the current WordPress
	 * query. Once we figure out which page we're viewing, we create a new
	 * `Query` object and let it build the breadcrumbs.
	 */
	public function build(): Contracts\Builder
	{
		// A hook for short-circuiting the breadcrumbs output and
		// running custom logic. Filters on this hook must return either
		// an instance of the `Contracts\Builder` interface after
		// running its own `build()` method or `null`.
		if ($builder = apply_filters('x3p0/breadcrumbs/builder/pre/build', null, $this)) {

			// Ensures that we only get `Builder` implementations.
			if (! is_subclass_of($builder, Contracts\Builder::class)) {
				throw new TypeError(esc_html(sprintf(
					// Translators: %1$s is a PHP class name, %2$s is the hook name.
					__('Only %1$s classes can be returned when filtering %2$s', 'x3p0-ideas'),
					Contracts\Builder::class,
					'x3p0/breadcrumbs/builder/pre/build'
				)));
			}

			return $builder;
		}

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
		return $this->crumbs;
	}

	/**
	 * {@inheritdoc}
	 */
	public function query(string $name, array $params = []): void
	{
		$query = $this->environment->getQueries()->get(
			$name,
			$params + [ 'builder' => $this ]
		);

		$query && $query->query();
	}

	/**
	 * {@inheritdoc}
	 */
	public function assemble(string $name, array $params = []): void
	{
		$assembler = $this->environment->getAssemblers()->get(
			$name,
			$params + [ 'builder' => $this ]
		);

		$assembler && $assembler->assemble();
	}

	/**
	 * {@inheritdoc}
	 */
	public function addCrumb(string $name, array $params = []): void
	{
		if ($this->environment->getCrumbs()->has($name)) {
			$this->crumbs[] = $this->environment->getCrumbs()->get(
				$name,
				$params + [ 'builder' => $this ]
			);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getOption(string $name): mixed
	{
		return $this->options[$name] ?? null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getLabel(string $name): string
	{
		$labels = $this->getOption('labels');
		return $labels[$name] ?? '';
	}

	/**
	 * {@inheritdoc}
	 */
	public function mapRewriteTags(string $post_type): bool
	{
		$mappings = $this->getOption('map_rewrite_tags');
		return $mappings[$post_type] ?? true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPostTaxonomy(string $post_type): string
	{
		$taxes = $this->getOption('post_taxonomy');
		return $taxes[$post_type] ?? '';
	}

	/**
	 * Returns an array of default labels.
	 */
	protected function defaultLabels(): array
	{
		// phpcs:disable Generic.Functions.FunctionCallArgumentSpacing.TooMuchSpaceAfterComma
		return [
			'home'                => __('Home', 'x3p0-breadcrumbs'),
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
