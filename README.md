# X3P0: Breadcrumbs

![Mostly decorative banner that displays a screenshot of the Breadcrumbs block in action and Nova, the brand's mascot, in a wheat field holding a baguette.](/.wporg/banner-1544x500.png)

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

If you're using or building a classic theme, you can wrap the Breadcrumbs block markup inside the WordPress `do_blocks()` function to parse the markup with PHP. Place the following code in your `header.php` template or in another template where you want to output the breadcrumbs:

```php
<?= do_blocks('<!-- wp:x3p0/breadcrumbs /-->') ?>
```

Alternatively, feel free to fully customize output using PHP following the instructions under the Developers section.

## Developers

### Outputting Breadcrumbs with PHP

The plugin isn't just a simple block. Its foundation is actually a robust, object-oriented script for generating breadcrumbs for pretty much anything you throw at it.

#### Basic Breadcrumbs Implementation

The most straightforward way of displaying breadcrumbs is via the `breadcrumbs()` helper function, which is a wrapper around the `BreadcrumbsService` class, and calling its `render()` method:

```php
use function X3P0\Breadcrumbs\breadcrumbs;

echo breadcrumbs()->render();
```

It doesn't get any simpler than that for outputting breadcrumbs. Of course, that includes all the default configuration, which you might want to customize.

#### Defining Breadcrumbs Parameters

The `breadcrumbs()->render()` method accepts three optional parameters:

- **`breadcrumbsConfig`:** Accepts either an instance of the `BreadcrumbsConfig` class or an array of arguments for configuring breadcrumbs.
- **`markupConfig`:** Accepts either an instance of the `MarkupConfig` class or an array of arguments for configuring the final HTML markup of the breadcrumb trail.
- **`markupType`:** Accepts a string representing the markup type. The plugin's available types are `html` (default), `microdata`, and `rdfa`.

These parameters are described in the followup sections below. For now, just know that you have options for customizing the breadcrumbs to your liking.

Here's an example of configuring the breadcrumbs with array configs:

```php
use function X3P0\Breadcrumbs\breadcrumbs;

echo breadcrumbs()->render(
	breadcrumbsConfig: [],    // Optional: BreadcrumbsConfig or array
	markupConfig:      [],    // Optional: MarkupConfig or array
	markupType:        'html' // Optional: html, microdata, or rdfa
);
```

If you use array-type configuration for either `breadcrumbsConfig` or `markupConfig`, they are automatically converted to `BreadcrumbsConfig` and `MarkupConfig` instances for you. Using arrays is just for convenience.

But if you prefer to use the `*Config` classes, you're welcome to do that:

```php
use X3P0\Breadcrumbs\BreadcrumbsConfig;
use X3P0\Breadcrumbs\Markup\MarkupConfig;
use function X3P0\Breadcrumbs\breadcrumbs;

echo breadcrumbs()->render(
	breadcrumbsConfig: new BreadcrumbsConfig(), // Optional: BreadcrumbsConfig or array
	markupConfig:      new MarkupConfig(),      // Optional: MarkupConfig or array
	markupType:        'html'                   // Optional: html, microdata, or rdfa
);
```

#### Breadcrumbs Configuration

The `BreadcrumbsConfig` class accepts multiple parameters:

- **`mapRewriteTags:`** An array of post types and whether to generate breadcrumbs based on each post type's rewrite tags (e.g., `%year%`, `%monthnum%`, etc.). By default, this is set to `true` for any post type rewrite slug that has `%` character in it.
- **`postTaxonomy`:** An array of post types and which taxonomy to use in the breadcrumb trail for single posts. The array key must be valid post type names (e.g., `post`, `book`), and the array values must be valid taxonomy names (e.g., `category`, `genre`). By default, this is an empty array.
- **`labels`:** An array of internationalized crumb labels that can be customized:
	- **`home`:** `Home`
	- **`error_404`:** `404 Not Found`
	- **`archives`:** `Archives`
	- **`search`:** `Search results for: %s`
	- **`untitled`:** `Untitled`
	- **`paged`:** `Page %s`
	- **`paged_comments`:** `Comment Page %s`
	- **`archive_minute`:** `Minute %s`
	- **`archive_week`:** `Week %s`
	- **`archive_minute_hour`:** `%s`
	- **`archive_hour`:** `%s`
	- **`archive_day`:** `%s`
	- **`archive_month`:** `%s`
	- **`archive_year`:** `%s`
- **`network`:** Whether to show the network as part of the breadcrumb trail on multisite installations. Defaults to `false`.

Here is an example of disabling post rewrite tags and enabling the category taxonomy for single posts:

```php
use function X3P0\Breadcrumbs\breadcrumbs;

$breadcrumbs_config = [
	'mapRewriteTags' => ['post' => false],
	'postTaxonomy'   => ['post' => 'category']
];

echo breadcrumbs()->render(
	breadcrumbsConfig: $breadcrumbs_config,
	markupConfig:      [],    // Optional: MarkupConfig or array
	markupType:        'html' // Optional: html, microdata, or rdfa
);
```

If you prefer to work with the `BreadcrumbsConfig` class directly, use this:

```php
use X3P0\Breadcrumbs\BreadcrumbsConfig;
use function X3P0\Breadcrumbs\breadcrumbs;

$breadcrumbs_config = new BreadcrumbsConfig(
	mapRewriteTags: ['post' => false],
	postTaxonomy:   ['post' => 'category']
);

echo breadcrumbs()->render(
	breadcrumbsConfig: $breadcrumbs_config,
	markupConfig:      [],    // Optional: MarkupConfig or array
	markupType:        'html' // Optional: html, microdata, or rdfa
);
```

#### Markup Configuration

The `MarkupConfig` class accepts several parameters:

- **`namespace`:** Used as a prefix for classes in a BEM-style structure (e.g., `{namespace}__{element}--{modifier}`). Defaults to `breadcrumbs` (note: the Breadcrumbs block uses `wp-x3p0-block-breadcrumbs`).
- **`containerAttr`:** An array of HTML attributes and values to apply to the container:
	- **`class`:** `{namespace}`
	- **`role`:** `navigation`
	- **`aria-label`:** `Breadcrumbs`
	- **`data-wp-interactive`:** `x3p0/breadcrumbs`
	- **`data-wp-router-region`:** `breadcrumbs`
- **`showOnFront`:** Whether to show the breadcrumbs on the site front page. Defaults to `false`.
- **`showFirstCrumb`:** Whether to display the first (homepage) breadcrumb. Defaults to `true`.
- **`showLastCrumb`:** Whether to display the last (current page) breadcrumb. Defaults to `true`.
- **`linkLastCrumb`:** Whether to link the last (current page) breadcrumb. Defaults to `false`. The `showLastCrumb` parameter must be enabled for this to work.

Here is an example of using array-style formatting to disable the first breadcrumb and link the last one:

```php
use function X3P0\Breadcrumbs\breadcrumbs;

$markup_config = [
	'showFirstCrumb' => false,
	'linkLastCrumb'  => true
];

echo breadcrumbs()->render(
	breadcrumbsConfig: [],    // Optional: BreadcrumbsConfig or array
	markupConfig:      $markup_config,
	markupType:        'html' // Optional: html, microdata, or rdfa
);
```

Or if you prefer to work directly with the `MarkupConfig` class, use this method:

```php
use X3P0\Breadcrumbs\Markup\MarkupConfig;
use function X3P0\Breadcrumbs\breadcrumbs;

$markup_config = new MarkupConfig(
	showFirstCrumb: false,
	linkLastCrumb:  true
);

echo breadcrumbs()->render(
	breadcrumbsConfig: [],    // Optional: BreadcrumbsConfig or array
	markupConfig:      $markup_config,
	markupType:        'html' // Optional: html, microdata, or rdfa
);
```

#### Markup Types

The plugin comes with three classes for rending the final HTML of the breadcrumb trail, which are implementations of the `X3P0\Breadcrumbs\Markup\Markup` interface. Unless you're wanting to create your own markup implementations, you don't need to worry about those. Instead, you just need to know what types are available.

The `markupType` parameter of `breadcrumbs()->render()` can be one of three values:

- **`html`:** Renders a plain HTML list of breadcrumbs. This is the default.
- **`microdata`:** Renders an HTML list of breadcrumbs using Schema.org microdata.
- **`rdfa`:** Renders an RDFa (Resource Description Framework in Attributes) compliant HTML list of breadcrumbs (_recommended for most use cases_).

This example uses RDFa schema attributes:

```php
use function X3P0\Breadcrumbs\breadcrumbs;

echo breadcrumbs()->render(
	breadcrumbsConfig: [],    // Optional: BreadcrumbsConfig or array
	markupConfig:      [],    // Optional: MarkupConfig or array
	markupType:        'rdfa' // Optional: html, microdata, or rdfa
);
```

#### Putting It All Together

The PHP under the hood for rendering breadcrumbs is very complex because each WordPress site is unique and has different needs. But that doesn't mean the public API needs to be complex. With the convenience of the  `breadcrumbs()` wrapper function, you have everything you need for the majority of situations.

Here's a look at what a few config options could look like using array-style syntax:

```php
use function X3P0\Breadcrumbs\breadcrumbs;

echo breadcrumbs()->render(
	breadcrumbsConfig: [
		'mapRewriteTags' => ['post' => false],
		'postTaxonomy'   => ['post' => 'category']
	],
	markupConfig: [
		'showFirstCrumb' => false,
		'linkLastCrumb'  => true
	],
	markupType: 'rdfa'
);
```

Of course, you're welcome to continue using the `*Config` classes if you prefer less syntactic sugar and more direct access to objects:

```php
use X3P0\Breadcrumbs\BreadcrumbsConfig;
use X3P0\Breadcrumbs\Markup\MarkupConfig;
use function X3P0\Breadcrumbs\breadcrumbs;

echo breadcrumbs()->render(
	breadcrumbsConfig: new BreadcrumbsConfig(
		mapRewriteTags: ['post' => false],
		postTaxonomy:   ['post' => 'category']
	),
	markupConfig: new MarkupConfig(
		showFirstCrumb: false,
		linkLastCrumb:  true
	),
	markupType: 'rdfa'
);
```

### Advanced Use Cases

#### Outputting JSON Linked Data (JSON-LD)

The plugin has a markup implementation for outputting JSON-LD style formats in the `<head>` of your webpage. This feature is not currently enabled by default since the plugin's primary purpose is to make displayable breadcrumbs via the block editor.

To enable it, you can call the normal `breadcrumbs()->render()` method and just pass in the `json-ld` markup type. And, of course, hook it to `wp_head`:

```php
use function X3P0\Breadcrumbs\breadcrumbs;

add_action('wp_head', function() {
	echo breadcrumbs()->render(markupType: 'json-ld');
});
```

#### Available Hooks

The plugin comes with a few hooks that you might find useful for advanced use cases.

##### `x3p0/breadcrumbs/register`

Fires immediately after the `X3P0\Breadcrumbs\Plugin` class registers its default bindings with the container and registers service providers. The plugin object is passed to actions attached to the hook.

```php
do_action('x3p0/breadcrumbs/register', $plugin);
```

##### `x3p0/breadcrumbs/booted`

Fires immediately after the `X3P0\Breadcrumbs\Plugin` class has booted all registered service providers. The plugin object is passed to actions attached to the hook.

```php
do_action('x3p0/breadcrumbs/booted', $plugin);
```

##### `x3p0/breadcrumbs/resolve/query-type`

This fires just after the resolution of the `Query` class to call when the breadcrumbs are just being collected. These are mapped to WordPress conditional tags to determine which page to show. Generally speaking, if you want to change which breadcrumbs are shown for a particular page, this is what you hook into and change the query type.

```php
apply_filters('x3p0/breadcrumbs/resolve/query-type', $queryType);
```

For example, if you've registered a custom `Query` class (see below), you might want it to execute when your particular plugin's page(s) are active:

```php
add_filter(
	'x3p0/breadcrumbs/resolve/query-type',
	fn($queryType) => $your_condition ? 'custom-query' : $queryType
);
```

#### Registering Custom Queries, Assemblers, and Crumbs

There may be times when you need to register custom `Query`, `Assembler`, and `Crumb` classes for custom use cases. The following is a quick example of how to register these:

```php
use X3P0\Breadcrumbs\Assembler\AssemblerRegistry;
use X3P0\Breadcrumbs\Crumb\CrumbRegistry;
use X3P0\Breadcrumbs\Query\QueryRegistry;

add_action('x3p0/breadcrumbs/boot', function($plugin) {
	$plugin->container()->get(QueryRegistry::class)->register('your-query',YourQuery::class);
	$plugin->container()->get(AssemblerRegistry::class)->register('your-assembler',YourAssembler::class);
	$plugin->container()->get(CrumbRegistry::class)->register('your-crumb',YourCrumb::class);
});
```

Please study the plugin's existing query, assembler, and crumb classes if you need to understand the conventions and, more precisely, the interfaces to use.

## License

X3P0 Breadcrumbs is licensed under the GPL version 3.0 or later.

The project includes resources from [Material Icons](https://fonts.google.com/icons), which are licensed under [Apache 2.0](http://www.apache.org/licenses/LICENSE-2.0.txt).
