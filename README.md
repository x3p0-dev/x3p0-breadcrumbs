# X3P0: Breadcrumbs

![Mostly decorative banner that displays a screenshot of the Breadcrumbs block in action.](/.wporg/banner-1544x500.png)

The X3P0: Breadcrumbs plugin is a breadcrumbs plugin reimagined for a modern, block-based WordPress.

In 2009, I [launched the first version of this script](https://justintadlock.com/archives/2009/04/05/breadcrumb-trail-wordpress-plugin) as a WordPress plugin. And I've continually refined it ever since. Today, this plugin exists as a WordPress block, all built upon a solid OOP foundation that's more powerful than ever. The block is the easy-to-use version that regular, everyday WordPress users can simply plug into their site and go about their business. But the stuff under the hood gives developers an insane amount of control to customize breadcrumbs to suit their needs.

## Usage

### From the Editor

You can add the **Breadcrumbs** block via the block inserter in the editor anywhere. This can include post, pages, templates, parts, and patterns. Just insert it and configure the settings however you like. Preferably, you'd add it to something like your **Header** template part so that it gets reused across the site.

### Block Themes

If you're a block theme author, you can include support for the Breadcrumbs block in your theme by adding the following block markup to a template, part, or pattern:

```html
<!-- wp:x3p0/breadcrumbs /-->
```

That will output the block with the defaults. Of course, you can configure it by customizing the attributes available for the block (see [`block.json`](https://github.com/x3p0-dev/x3p0-breadcrumbs/blob/master/resources/blocks/breadcrumbs/block.json) for all attributes). Here is an example that changes the `separatorIcon` attribute to show an arrow:

```html
<!-- wp:x3p0/breadcrumbs {"separatorIcon":"svg-arrow"} /-->
```

### Classic Themes

If you're using or building a classic theme, you can wrap the Breadcrumbs block markup inside the WordPress `do_blocks()` function to parse the markup with PHP. Place the following code in your `header.php` template on in another template where you want to output the breadcrumbs:

```php
<?= do_blocks('<!-- wp:x3p0/breadcrumbs /-->') ?>
```

Alternatively, feel free to fully customize output using PHP following the instructions under the Developers section.

## Developers

### Outputting Breadcrumbs With PHP

The plugin isn't just a simple block. The plugin's foundation is actually a robust, object-oriented script for generating breadcrumbs for pretty much anything you throw at it.

This foundation includes three primary interfaces:

- [`X3P0\Breadcrumbs\Contracts\Environment`](https://github.com/x3p0-dev/x3p0-breadcrumbs/blob/master/src/Contracts/Environment.php): Defines the query, assembler, and crumb classes that are used for generating breadcrumbs.
- [`X3P0\Breadcrumbs\Contracts\Builder`](https://github.com/x3p0-dev/x3p0-breadcrumbs/blob/master/src/Contracts/Builder.php): Generates the breadcrumb items ("crumbs").
- [`X3P0\Breadcrumbs\Contracts\Markup`](https://github.com/x3p0-dev/x3p0-breadcrumbs/blob/master/src/Contracts/Markup.php): Renders the markup for a breadcrumb trail.

By default, the plugin includes one implementation for both the `Environment` and `Builder` interfaces. It includes three implementations of the `Markup` interface for outputting plain HTML, microdata-formatted, and RDFa-formatted lists.

```php
use X3P0\Breadcrumbs\Builder\TrailBuilder;
use X3P0\Breadcrumbs\Environment\Environment;
use X3P0\Breadcrumbs\Markup\Html;

$environment = new Environment();
$builder     = new TrailBuilder($environment);
$markup      = new Html($builder);

echo $markup->render();
```

Or, if you wanted, you could shorten that to:

```php
use X3P0\Breadcrumbs\Builder\TrailBuilder;
use X3P0\Breadcrumbs\Environment\Environment;
use X3P0\Breadcrumbs\Markup\Html;

echo (new Html(new TrailBuilder(new Environment())))->render();
```

### Environment Configuration

Normally, you wouldn't need to configure the environment (the plugin covers most use cases). But if you need to add custom query, assembler, or crumb implementations, you can pass these as arrays as long as the values of those values are implementations of their given contracts:

- [`X3P0\Breadcrumbs\Contracts\Query`](https://github.com/x3p0-dev/x3p0-breadcrumbs/blob/master/src/Contracts/Query.php): Defines a query class for executing based on the current WordPress query.
- [`X3P0\Breadcrumbs\Contracts\Assembler`](https://github.com/x3p0-dev/x3p0-breadcrumbs/blob/master/src/Contracts/Assembler.php): Helper classes used for determining which breadcrumbs to show based on the query.
- [`X3P0\Breadcrumbs\Contracts\Crumb`](https://github.com/x3p0-dev/x3p0-breadcrumbs/blob/master/src/Contracts/Crumb.php): A class for handling the output of an individual breadcrumb item.

An example of passing custom queries, builders, and crumbs:

```php
use X3P0\Breadcrumbs\Environment\Environment;

$environment = new Environment(
	[ 'example-query'   => ExampleQuery::class ],
	[ 'example-builder' => ExampleBuilder::class ],
	[ 'example-crumb'   => ExampleCrumb::class ]
)
```

If you're a third-party plugin developer, you can also hook into the existing environment and customize it via the `x3p0/breadcrumbs/environment` hook:

```php
use X3P0\Breadcrumbs\Contracts\Environment;

do_action('x3p0/breadcrumbs/environment', function(Environment $environment) {
	$environment->crumbRegistry()->register('example-crumb', ExampleCrumb::class);
});
```

### Builder Configuration

The `Builder` class accepts two parameters:

- **`environment`:** An implementation of the `X3P0\Breadcrumbs\Contracts\Environment` interface.
- **`options`:** A configurable array of options for customizing how the breadcrumbs are generated:
	- **`labels`:** An array of internationalized crumb labels that can be customized:
		- **`home`:** `Home`
		- **`error_404`:** `404 Not Found`
		- **`archives`:** `Archives`
		- **`search`:** `Search results for: %s`
		- **`paged`:** `Page %s`
		- **`paged_comments`:** `Comment Page %s`
		- **`archive_minute`:** `Minute %s`
		- **`archive_week`:** `Week %s`
		- **`archive_minute_hour`:** `%s`
		- **`archive_hour`:** `%s`
		- **`archive_day`:** `%s`
		- **`archive_month`:** `%s`
		- **`archive_year`:** `%s`
	- **`map_rewrite_tags:`** An array of post types and whether to generate breadcrumbs based on the post type's rewrite tags (e.g., `%year%`, `%monthnum%`, etc.). By default, if this is not set for a post type, it will be `false`. The array keys must be valid post type names (e.g., `post`, `book`), and the array values must be a boolean value. The `post` post type is `true` by default.
	- **`network`:** Whether to show the network as part of the breadcrumb trail on multisite installations. Defaults to `false`.
	- **`post_taxonomy`:** An array of post types and which taxonomy to use in the breadcrumb trail for single posts. The array key must be valid post type names (e.g., `post`, `book`), and the array values must be valid taxonomy names (e.g., `category`, `genre`). By default, this is an empty array.

Here is an example of disabling post rewrite tags and enabling the category taxonomy for single posts:

```php
use X3P0\Breadcrumbs\Builder\TrailBuilder;
use X3P0\Breadcrumbs\Environment\Environment;
use X3P0\Breadcrumbs\Markup\Html;

$builder_options = [
	'map_rewrite_tags' => [
		'post' => false,
	],
	'post_taxonomy' => [
		'post' => 'category'
	]
];

$environment = new Environment();
$builder     = new TrailBuilder($environment, $builder_options);
$markup      = new Html($builder);

echo $markup->render();
```

These options are also configurable via the `x3p0/breadcrumbs/builder/config` filter hook:

```php
add_filter('x3p0/breadcrumbs/builder/config', fn(array $options) => $options);
```

### Builder Filter

If you're a third-party plugin developer, you can short-circuit the builder to run custom queries before the plugin does its own thing. In this case, your plugin should either return the builder implementation or the original value (default is `null`):

```php
use X3P0\Breadcrumbs\Contracts\Builder;

add_filter('x3p0/breadcrumbs/builder/pre/build', function($pre, Builder $builder) {
	if (is_your_custom_conditional()) {
		$builder->query('custom-query-name');
		return $builder;
	}

	return $pre;
}, 10, 2);
```

### Markup Configuration

The `Html`, `Microdata`, and `Rdfa` classes, each of which are implementations of the `Markup` interface, accept two parameters:

- **`builder`:** An implementation of the `X3P0\Breadcrumbs\Contracts\Builder` interface.
- **`options`:** A configurable array of options for customizing how the markup is generated:
	- **`show_on_front`:** Whether to show the breadcrumbs on the site front page. Defaults to `false`.
	- **`show_first_item`:** Whether to display the first breadcrumb item (usually the home page). Defaults to `true`.
	- **`show_last_item`:** Whether to display the last breadcrumb item (usually the current page). Defaults to `true`.
	- **`before`:** Custom HTML to add before the HTML output. Defaults to an empty string.
	- **`after`:** Custom HTML to add after the HTML output. Defaults to an empty string.
        - **`container_attr`:** An array of HTML attributes and values to apply to the container.

Here is an example of using Schema.org microdata (via the `Microdata` class) and configuring the options to remove the first item:

```php
use X3P0\Breadcrumbs\Builder\TrailBuilder;
use X3P0\Breadcrumbs\Environment\Environment;
use X3P0\Breadcrumbs\Markup\Microdata;

$markup_options = [
	'show_first_item' => false
];

$environment = new Environment();
$builder     = new TrailBuilder($environment);
$markup      = new Microdata($builder, $markup_options);

echo $markup->render();
```

These options are also configurable via the `x3p0/breadcrumbs/markup/config` filter hook:

```php
add_filter('x3p0/breadcrumbs/markup/config', fn(array $options) => $options);
```

### Markup Implementations

The plugin comes with three classes, which are implementations of the `X3P0\Breadcrumbs\Contracts\Markup` interface, for rending the final HTML of the breadcrumb trail.

- **`X3P0\Breadcrumbs\Markup\Html`:** Renders a plain HTML list of breadcrumbs.
- **`X3P0\Breadcrumbs\Markup\Microdata`:** Renders an HTML list of breadcrumbs using Schema.org microdata.
- **`X3P0\Breadcrumbs\Markup\Rdfa`:** Renders an RDFa (Resource Description Framework in Attributes) compliant HTML list of breadcrumbs.

Here's an example of swapping out the `Html` implementation shown earlier with the `Rdfa` implementation:

```php
use X3P0\Breadcrumbs\Builder\TrailBuilder;
use X3P0\Breadcrumbs\Environment\Environment;
use X3P0\Breadcrumbs\Markup\Rdfa;

$environment = new Environment();
$builder     = new TrailBuilder($environment);
$markup      = new Rdfa($builder);

echo $markup->render();
```

You are, of course, free to build your own implementation too. Any class that implements the `X3P0\Breadcrumbs\Contracts\Markup` interface will work.

## License

X3P0 Breadcrumbs is licensed under the GPL version 3.0 or later.

The project includes resources from [Material Icons](https://fonts.google.com/icons), which are licensed under [Apache 2.0](http://www.apache.org/licenses/LICENSE-2.0.txt).
