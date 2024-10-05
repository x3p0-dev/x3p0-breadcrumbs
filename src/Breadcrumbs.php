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

use X3P0\Breadcrumbs\Contracts\Breadcrumbs as BreadcrumbsContract;

class Breadcrumbs implements BreadcrumbsContract
{
	/**
	 * The parsed arguments passed into the class.
	 *
	 * @since 1.0.0
	 */
	protected array $args = [];

	/**
	 * Array of `Crumb` objects.
	 *
	 * @since 1.0.0
	 */
	protected array $crumbs = [];

	/**
	 * Creates a new breadcrumbs object.
	 *
	 * @since 1.0.0
	 */
	public function __construct(array $args = [])
	{
		$defaults = [
			'labels'             => [],
			'post_taxonomy'      => [],
			'show_on_front'      => false,
			'show_trail_end'     => true,
			'network'            => false,
			'before'             => '',
			'after'              => '',
			'container_tag'      => 'nav',
			'title_tag'          => 'h2',
			'list_tag'           => 'ol',
			'item_tag'           => 'li',
			'container_class'    => 'breadcrumbs',
			'title_class'        => 'breadcrumbs__title',
			'list_class'         => 'breadcrumbs__trail',
			'item_class'         => 'breadcrumbs__crumb',
			'item_content_class' => 'breadcrumbs__crumb-content',
			'item_label_class'   => 'breadcrumbs__crumb-label',
			'show_home_label'    => true,
			'post_rewrite_tags'  => true,
			'post'               => null,
			'post_type'          => null,
			'term'               => null,
			'user'               => null
		];

		$this->args = wp_parse_args($args, $defaults);

		$this->args['labels'] = wp_parse_args(
			$this->args['labels'],
			$this->defaultLabels()
		);

		$this->args['post_taxonomy'] = wp_parse_args(
			$this->args['post_taxonomy'],
			$this->defaultPostTaxonomies()
		);

		// Allow developers to filter the arguments.
		$this->args = apply_filters(
			'x3p0/breadcrumbs/args',
			$this->args,
			$this
		);
	}

	/**
	 * Returns an array of default labels.
	 *
	 * @since 1.0.0
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
	 *
	 * @since 1.0.0
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

	/**
	 * Returns an array of `Crumb` objects.
	 *
	 * @since 1.0.0
	 */
	public function all(): array
	{
		return $this->crumbs;
	}

	/**
	 * Renders the breadcrumbs HTML output. Note that this output is
	 * escaped earlier on render.
	 *
	 * @since 1.0.0
	 */
	public function display(): void
	{
		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $this->render();
	}

	/**
	 * Returns the breadcrumbs HTML output.
	 *
	 * @since 1.0.0
	 */
	public function render(): string
	{
		$html = $container = $list = $title = '';

		// Get an array of all the available breadcrumbs from the builder.
		$crumbs = $this->all();

		if (! $crumbs) {
			return $html;
		}

		// HTML allowed in labels. Everything else gets stripped out.
		$allowed_html = [
			'abbr'    => [ 'title' => true ],
			'acronym' => [ 'title' => true ],
			'code'    => true,
			'em'      => true,
			'strong'  => true,
			'i'       => true,
			'b'       => true
		];

		$count     = count($crumbs);
		$i         = 1;
		$show_last = $this->option('show_trail_end');

		// Loop through each of the crumbs and build out a list.
		foreach ($crumbs as $crumb) {
			// Add `.screen-reader-text` class for crumbs
			// with hidden labels. Usually applied to the
			// home crumb when it's replaced with an icon.
			$hidden = $crumb->visuallyHidden()
				? ' screen-reader-text'
				: '';

			// Break out of the loop if this is the last item
			// and we're not supposed to show the trail end.
			if ($i === $count && ! $show_last) {
				break;
			}

			// Filter out any unwanted HTML from the label.
			$label = sprintf(
				'<span class="%s" itemprop="name">%s</span>',
				esc_attr($this->option('item_label_class') . $hidden),
				wp_kses($crumb->label(), $allowed_html)
			);

			// Get the crumb URL.
			$url = $crumb->url();

			// Wrap the label with a link if the crumb has
			// one and this isn't the last item.
			if ($url && $i !== $count) {
				$item = sprintf(
					'<a href="%s" class="%s" itemprop="item">%s</a>',
					esc_url($url),
					esc_attr($this->option('item_content_class')),
					$label
				);
			} else {
				$item = sprintf(
					'<span class="%s" itemscope itemid="%s" itemtype="https://schema.org/WebPage" itemprop="item">%s</span>',
					esc_attr($this->option('item_content_class')),
					esc_url($url),
					$label
				);
			}

			// Get the base class to build modifier classes from.
			$base_class = explode(' ', $this->option('item_class'));
			$base_class = array_shift($base_class);

			$classes = [
				$this->option('item_class'),
				sprintf("{$base_class}--%s", $crumb->type())
			];

			// Build the list item.
			$list .= sprintf(
				'<%1$s class="%2$s" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">%3$s<meta itemprop="position" content="%4$s"/></%1$s>',
				tag_escape($this->option('item_tag')),
				esc_attr(join(' ', $classes)),
				$item,
				$i
			);

			++$i;
		}

		// Build the list HTML.
		$list = sprintf(
			'<%1$s class="%2$s" itemscope itemtype="https://schema.org/BreadcrumbList">%3$s</%1$s>',
			tag_escape($this->option('list_tag')),
			esc_attr($this->option('list_class')),
			$list
		);

		// Build the title HTML only if there's a label for it.
		if ($this->label('title')) {
			$title = sprintf(
				'<%1$s class="%2$s">%3$s</%1$s>',
				tag_escape($this->option('title_tag')),
				esc_attr($this->option('title_class')),
				$this->label('title')
			);
		}

		if ($this->option('container_tag')) {
			$container = sprintf(
				'<%1$s class="%2$s" role="navigation" aria-label="%3$s" itemprop="breadcrumb">%4$s</%1$s>',
				tag_escape($this->option('container_tag')),
				esc_attr($this->option('container_class')),
				esc_attr($this->label('aria_label')),
				'%1$s%2$s'
			);
		}

		// Build out the final breadcrumbs trail HTML.
		$html = sprintf($container ?: '%1$s%2$s', $title, $list);

		// Add before/after wrappers and return.
		return $this->option('before') . $html . $this->option('after');
	}

	/**
	 * Runs through a series of conditionals based on the current WordPress
	 * query. Once we figure out which page we're viewing, we create a new
	 * `Query` object and let it build the breadcrumbs.
	 *
	 * @since 1.0.0
	 */
	public function make(): BreadcrumbsContract
	{
		// Call the query class associated with an object passed in.
		// phpcs:disable
		if ($this->option('post')) {
			$this->query('Singular', [ 'post' => $this->option('post') ]);
		} elseif ($this->option('post_type')) {
			$this->query('PostTypeArchive', [ 'post_type' => $this->option('post_type') ]);
		} elseif ($this->option('term')) {
			$this->query('Tax', [ 'term' => $this->option('term') ]);
		} elseif ($this->option('user')) {
			$this->query('Author', [ 'user' => $this->option('user') ]);
		}

		// This may not follow any sort of standards-based code
		// formatting rules, but you can damn well read it better!
		elseif (is_front_page()) { $this->query('FrontPage'); }
		elseif (is_home()      ) { $this->query('Home'     ); }
		elseif (is_singular()  ) { $this->query('Singular' ); }
		elseif (is_archive()   ) { $this->query('Archive'  ); }
		elseif (is_search()    ) { $this->query('Search'   ); }
		elseif (is_404()       ) { $this->query('Error'    ); }
		elseif (is_paged()     ) { $this->query('Paged'    ); }
		// phpcs:enable

		// Return the object for chaining methods.
		return $this;
	}

	/**
	 * Creates a new `Query` object and runs its `make()` method.
	 *
	 * @since 1.0.0
	 */
	public function query(string $type, array $data = []): void
	{
		$class = "\\X3P0\\Breadcrumbs\\Query\\{$type}";
		$query = new $class($this, $data);

		$query->make();
	}

	/**
	 * Creates a new `Build` object and runs its `make()` method.
	 *
	 * @since 1.0.0
	 */
	public function build(string $type, array $data = []): void
	{
		$class = "\\X3P0\\Breadcrumbs\\Build\\{$type}";
		$build = new $class($this, $data);

		$build->make();
	}

	/**
	 * Creates a new `Crumb` object and adds it to the array of crumbs.
	 *
	 * @since 1.0.0
	 */
	public function crumb(string $type, array $data = []): void
	{
		$class = "\\X3P0\\Breadcrumbs\\Crumb\\{$type}";
		$this->crumbs[] = new $class($this, $data);
	}

	/**
	 * Returns a specific option or `false` if the option doesn't exist.
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public function option(string $name)
	{
		return isset($this->args[ $name ]) ? $this->args[ $name ] : false;
	}

	/**
	 * Returns a specific label or an empty string if it doesn't exist.
	 *
	 * @since 1.0.0
	 */
	public function label(string $name): string
	{
		$labels = $this->option('labels');

		return $labels[ $name ] ?? '';
	}

	/**
	 * Returns a specific post taxonomy or an empty string if one isn't set.
	 *
	 * @since 1.0.0
	 */
	public function postTaxonomy(string $post_type): string
	{
		$taxes = $this->option('post_taxonomy');

		return $taxes[ $post_type ] ?? '';
	}
}
