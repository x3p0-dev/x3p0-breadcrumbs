# X3P0: Breadcrumbs

![Mostly decorative banner that displays a screenshot of the Breadcrumbs block in action and Nova, the brand's mascot, in a wheat field holding a baguette.](/.wporg/banner-1544x500.png)

The X3P0: Breadcrumbs plugin is a breadcrumbs plugin reimagined for a modern, block-based WordPress.

In 2009, I [launched the first version of this script](https://justintadlock.com/archives/2009/04/05/breadcrumb-trail-wordpress-plugin) as a WordPress plugin. And I've continually refined it ever since. Today, this plugin exists as a WordPress block, all built upon a solid OOP foundation that's more powerful than ever. The block is the easy-to-use version that regular, everyday WordPress users can simply plug into their site and go about their business. But the stuff under the hood gives developers an insane amount of control to customize breadcrumbs to suit their needs.

## Table of Contents

- [Usage](#usage)
	- [From the Editor](#from-the-editor)
	- [Block Themes](#block-themes)
	- [Classic Themes](#classic-themes)
- [Developers](#developers)
	- [Outputting Breadcrumbs with PHP](#outputting-breadcrumbs-with-php)
		- [Basic Breadcrumbs Implementation](#basic-breadcrumbs-implementation)
		- [Defining Breadcrumbs Parameters](#defining-breadcrumbs-parameters)
		- [Breadcrumbs Configuration](#breadcrumbs-configuration)
		- [Markup Configuration](#markup-configuration)
		- [Markup Types](#markup-types)
		- [Putting It All Together](#putting-it-all-together)
	- [Advanced Use Cases](#advanced-use-cases)
		- [Outputting JSON Linked Data (JSON-LD)](#outputting-json-linked-data-json-ld)
		- [Modifying the Breadcrumb Trail with Events](#modifying-the-breadcrumb-trail-with-events)
		- [Available Hooks](#available-hooks)
		- [Building Custom Query, Assembler, Crumb, and Markup Types](#building-custom-query-assembler-crumb-and-markup-types)
		- [The Extension System](#the-extension-system)
- [License](#license)

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

The most straightforward way of displaying breadcrumbs is via the `breadcrumbs()` helper function, which returns the shared `BreadcrumbsRenderer` instance, and calling its `render()` method:

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

- **`labels`:** An array of internationalized crumb labels that can be customized:
	- **`home`:** `Home`
	- **`untitled`:** `Untitled`
	- **`error_404`:** `Page not found`
	- **`archives`:** `Archives`
	- **`search`:** `Search results for: %s`
	- **`paged`:** `Page %s`
	- **`paged_comments`:** `Comment Page %s`
	- **`archive_hour`:** `Hour %s`
	- **`archive_minute`:** `Minute %s`
	- **`archive_second`:** `Second %s`
	- **`archive_week`:** `Week %s`
	- **`archive_day`:** `%s`
	- **`archive_month`:** `%s`
	- **`archive_year`:** `%s`
- **`mapRewriteTags:`** An array of post types and whether to generate breadcrumbs based on each post type's rewrite tags (e.g., `%year%`, `%monthnum%`, etc.). By default, this is set to `true` for any post type rewrite slug that has `%` character in it.
- **`postTaxonomy`:** An array of post types and which taxonomy to use in the breadcrumb trail for single posts. The array key must be valid post type names (e.g., `post`, `book`), and the array values must be valid taxonomy names (e.g., `category`, `genre`). By default, this is an empty array.
- **`network`:** Whether to show the network as part of the breadcrumb trail on multisite installations. Defaults to `false`.

Here is an example of disabling post rewrite tags and enabling the category taxonomy for single posts:

```php
use function X3P0\Breadcrumbs\breadcrumbs;

$breadcrumbsConfig = [
	'mapRewriteTags' => ['post' => false],
	'postTaxonomy'   => ['post' => 'category']
];

echo breadcrumbs()->render(
	breadcrumbsConfig: $breadcrumbsConfig,
	markupConfig:      [],    // Optional: MarkupConfig or array
	markupType:        'html' // Optional: html, microdata, or rdfa
);
```

If you prefer to work with the `BreadcrumbsConfig` class directly, use this:

```php
use X3P0\Breadcrumbs\BreadcrumbsConfig;
use function X3P0\Breadcrumbs\breadcrumbs;

$breadcrumbsConfig = new BreadcrumbsConfig(
	mapRewriteTags: ['post' => false],
	postTaxonomy:   ['post' => 'category']
);

echo breadcrumbs()->render(
	breadcrumbsConfig: $breadcrumbsConfig,
	markupConfig:      [],    // Optional: MarkupConfig or array
	markupType:        'html' // Optional: html, microdata, or rdfa
);
```

#### Markup Configuration

The `MarkupConfig` class accepts several parameters:

- **`namespace`:** Used as a prefix for classes in a BEM-style structure (e.g., `{namespace}__{element}--{modifier}`). Defaults to `breadcrumbs` (note: the Breadcrumbs block uses `wp-block-x3p0-breadcrumbs`).
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

$markupConfig = [
	'showFirstCrumb' => false,
	'linkLastCrumb'  => true
];

echo breadcrumbs()->render(
	breadcrumbsConfig: [],    // Optional: BreadcrumbsConfig or array
	markupConfig:      $markupConfig,
	markupType:        'html' // Optional: html, microdata, or rdfa
);
```

Or if you prefer to work directly with the `MarkupConfig` class, use this method:

```php
use X3P0\Breadcrumbs\Markup\MarkupConfig;
use function X3P0\Breadcrumbs\breadcrumbs;

$markupConfig = new MarkupConfig(
	showFirstCrumb: false,
	linkLastCrumb:  true
);

echo breadcrumbs()->render(
	breadcrumbsConfig: [],    // Optional: BreadcrumbsConfig or array
	markupConfig:      $markupConfig,
	markupType:        'html' // Optional: html, microdata, or rdfa
);
```

#### Markup Types

The plugin comes with three classes for rendering the final HTML of the breadcrumb trail, which are implementations of the `X3P0\Breadcrumbs\Markup\Markup` contract. Unless you're wanting to create your own markup implementations, you don't need to worry about those. Instead, you just need to know what types are available.

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

#### Modifying the Breadcrumb Trail with Events

The plugin dispatches typed events at the key moments while a trail is built and rendered. Listening to these events is the recommended way to change which breadcrumbs are shown, to adjust the finished trail, or to retarget how it is rendered. Each event is also bridged to a matching WordPress action, so you can use a plain `add_action()` callback.

There are three events:

- **`Query\Event\QueryTypeResolving`:** Dispatched while resolving which query type matches the current request, _before_ the query runs. This is what you hook into to change which breadcrumbs are shown for a given page.
- **`Crumb\Event\CrumbsBuilt`:** Dispatched _after_ the trail has been built, before it is returned. This is what you hook into to append, remove, or relabel crumbs.
- **`Markup\Event\MarkupRendering`:** Dispatched _after_ the trail is built but _before_ it is rendered. This is what you hook into to change the markup type or config used to render the trail for a given request.

##### Changing the Query Type

The `QueryTypeResolving` event carries `$event->queryType` — the type detected from the current request: a `QueryType` case for a built-in type, a `Query` class-string for a custom one, or `null` when nothing matched — and `$event->breadcrumbsConfig`, the active `BreadcrumbsConfig` for this build. Reassign `$event->queryType` to override it, to a `QueryDefinition` case, a `Query` class-string, or `null` to build no breadcrumbs.

The simplest way to hook in is the bridged action, which passes the event object:

```php
use X3P0\Breadcrumbs\Query\Event\QueryTypeResolving;

add_action(QueryTypeResolving::NAME, function (QueryTypeResolving $event) {
	if ($yourCondition) {
		$event->queryType = MyQuery::class;
	}
});
```

Alternatively, register a typed listener directly on the dispatcher. Do this on the `x3p0/breadcrumbs/register` action (documented below), where the plugin — and therefore the container — is available.

##### Adjusting the Finished Crumbs

The `CrumbsBuilt` event carries:

- **`$event->crumbs`:** The finished `CrumbCollection` — the same mutable instance the caller receives, so changes you make here are what ultimately gets rendered.
- **`$event->makeCrumb()` / `$event->addCrumb()`:** Build new crumbs through the plugin's factory (see below).

Every crumb in the collection is a `Crumb` object exposing `getSlug()` (its type slug, e.g. `post`, used in the `crumb--{slug}` CSS class), `getLabel()`, and `getUrl()`.

**Finding crumbs.** Every lookup method takes a callback matching the crumb itself. Match on the crumb's class — with `whereInstanceOf()`, an `instanceof` check inside a callback, or `replaceInstanceWhere()` (below) — rather than a string identifier. Your editor gets full type hinting this way, and you can safely read the crumb's typed properties (e.g. a `Term` crumb's `$term`, a `Post` crumb's `$post`):

- **`first(?callable $callback = null): ?Crumb`:** The first crumb, or the first matching the callback.
- **`last(?callable $callback = null): ?Crumb`:** The last crumb, or the last matching the callback.
- **`contains(callable $callback): bool`:** Whether any crumb satisfies the callback.
- **`every(callable $callback): bool`:** Whether every crumb satisfies the callback.
- **`filter(callable $callback): CrumbCollection`:** A new collection of the crumbs that do satisfy the callback.
- **`reject(callable $callback): CrumbCollection`:** A new collection of the crumbs that don't satisfy the callback.
- **`whereInstanceOf(string $class): CrumbCollection`:** A new collection of the crumbs that are instances of the class or interface.

Add `count()`, `isEmpty()`, `isNotEmpty()`, `all()`, `map()`, and `reduce()` for inspecting the collection as a whole.

**Building a crumb.** To create a crumb to add or use as a replacement, call `$event->makeCrumb()` with a `CrumbType` case (for a built-in type) or a `Crumb` class-string (for a custom one), plus its constructor parameters. It returns the crumb *without* adding it to the trail:

```php
$crumb = $event->makeCrumb(MyCrumb::class, ['foo' => $bar]);
```

To build *and* append in a single step, use `$event->addCrumb()` instead.

**Adding crumbs:**

- **`push(Crumb $crumb): void`:** Append to the end of the trail.
- **`prepend(Crumb $crumb): void`:** Insert at the front.
- **`insertBefore(Crumb $target, Crumb $crumb): void`:** Insert before a crumb you found. Does nothing if the target isn't in the trail.
- **`insertAfter(Crumb $target, Crumb $crumb): void`:** Insert after a crumb you found. Does nothing if the target isn't in the trail.

**Removing crumbs:**

- **`removeWhere(callable $callback): void`:** Remove every crumb that satisfies the callback (e.g. an `instanceof` check).
- **`pop(): ?Crumb`** / **`shift(): ?Crumb`:** Remove and return the last/first crumb.

**Replacing crumbs:**

- **`replace(Crumb $existing, Crumb $replacement): void`:** Swap a specific crumb in place, keeping its position.
- **`replaceWhere(callable $callback, callable $replacement): void`:** Swap every matching crumb in place. The replacement callback receives the matched crumb, which is handy for wrapping or relabeling it.
- **`replaceInstanceWhere(string $class, callable $callback, callable $replacement): void`:** Swap every crumb that is an instance of `$class` and satisfies the callback. Both callbacks receive only crumbs of the class, so they may typehint it directly.

Here's a practical example that removes the home crumb, relabels the post type archive, and inserts a crumb after it:

```php
use X3P0\Breadcrumbs\Crumb\Crumb;
use X3P0\Breadcrumbs\Crumb\Event\CrumbsBuilt;
use X3P0\Breadcrumbs\Crumb\Type\Home;
use X3P0\Breadcrumbs\Crumb\Type\PostType;

add_action(CrumbsBuilt::NAME, function (CrumbsBuilt $event) {
	// Remove the home crumb.
	$event->crumbs->removeWhere(fn (Crumb $crumb) => $crumb instanceof Home);

	// Insert a custom crumb right after the post type archive crumb.
	if ($archive = $event->crumbs->first(fn (Crumb $crumb) => $crumb instanceof PostType)) {
		$event->crumbs->insertAfter(
			$archive,
			$event->makeCrumb(MyCrumb::class)
		);
	}
});
```

As with the query event, you may register a typed listener for `CrumbsBuilt::class` on the dispatcher instead of using the action bridge — see [The Extension System](#the-extension-system) below for the tidiest way to do that.

##### Retargeting the Rendered Markup

The `MarkupRendering` event fires after the trail is built but before it is rendered, so it is where you change *how* a finished trail is presented for the current request. It carries three things:

- **`$event->crumbs`:** The finished `CrumbCollection`, read-only. Mutate crumbs on `CrumbsBuilt` instead; by this point the trail is fixed and only its presentation is in play.
- **`getMarkupType()` / `setMarkupType()`:** The markup type to render with. Pass a `MarkupType` case for a built-in format or a string key for a custom one.
- **`getConfig()` / `setConfig()`:** The `MarkupConfig` to render with.

For example, render the trail with microdata on your shop's pages while leaving every other request untouched:

```php
use X3P0\Breadcrumbs\Markup\Event\MarkupRendering;
use X3P0\Breadcrumbs\Markup\MarkupType;

add_action(MarkupRendering::NAME, function (MarkupRendering $event) {
	if (function_exists('is_shop') && is_shop()) {
		$event->setMarkupType(MarkupType::Microdata);
	}
});
```

As with the other events, you may register a typed listener for `MarkupRendering::class` on the dispatcher instead of using the action bridge.

#### Available Hooks

Beyond the event bridges above, the plugin fires one lifecycle action.

##### `x3p0/breadcrumbs/register`

Fires immediately after the `X3P0\Breadcrumbs\Plugin` class registers its default bindings and service providers, but before they boot. The plugin object is passed to attached callbacks, giving you access to the container to register your own services, listeners, or custom types. This is the correct hook for any of the registration examples in this section.

```php
do_action('x3p0/breadcrumbs/register', $plugin);
```

#### Building Custom Query, Assembler, Crumb, and Markup Types

There may be times when you need custom `Query`, `Assembler`, `Crumb`, or `Markup` classes for custom use cases. There's no registry to populate — each subsystem's factory resolves a class directly through the container, so you just extend the subsystem's abstract base (`Query`, `Assembler`, or `Crumb`) and dispatch to it by its `::class` name wherever the plugin accepts that subsystem's type:

```php
namespace MyPlugin\Breadcrumbs;

use X3P0\Breadcrumbs\Query\Query;

final class MyQuery extends Query
{
	public function query(): void
	{
		$this->context->addCrumb(/* ... */);
	}
}
```

```php
use MyPlugin\Breadcrumbs\MyQuery;
use X3P0\Breadcrumbs\Query\Event\QueryTypeResolving;

add_action(QueryTypeResolving::NAME, function (QueryTypeResolving $event) {
	if ($yourCondition) {
		$event->queryType = MyQuery::class;
	}
});
```

The same pattern applies to a custom `Assembler` — dispatched via `$context->assemble(MyAssembler::class)` — and a custom `Crumb` — dispatched via `$context->makeCrumb(MyCrumb::class)` or `$context->addCrumb(MyCrumb::class)`. Please study the plugin's existing classes under `src/` if you need to understand the conventions and, more precisely, the abstract contracts to extend.

Note that `$context` isn't the same object for every subsystem: a `Query` receives a `QueryContext` (`query()`, `assemble()`, `makeCrumb()`, `addCrumb()`), while an `Assembler` receives the narrower `AssemblerContext` (everything but `query()`) — an assembler can delegate to other assemblers and build crumbs, but it cannot dispatch a query, by design. A `Crumb` doesn't receive a context at all; its constructor takes `BreadcrumbsConfig $config` directly, since a crumb only ever needs to read config to produce its label and URL.

##### Custom Markup Types

A custom `Markup` type works the same way: extend `Markup` and pass its `::class` name as `markupType` (or to `setMarkupType()` on the `MarkupRendering` event). If you also want it selectable by a short string key — for `markupType: 'my-key'`, or as an option in the block editor's markup control — implement `MarkupBlockOption` and tag the class under `Markup::TAG`:

```php
use X3P0\Breadcrumbs\Markup\Markup;
use X3P0\Breadcrumbs\Markup\MarkupBlockOption;

final class MyMarkup extends Markup implements MarkupBlockOption
{
	public static function key(): string
	{
		return 'my-key';
	}

	public static function label(): string
	{
		return __('My Markup', 'my-plugin');
	}

	public function render(): string
	{
		// ...
	}
}
```

```php
use X3P0\Breadcrumbs\Markup\Markup;

add_action('x3p0/breadcrumbs/register', function ($plugin) {
	$plugin->container()->tag(MyMarkup::class, Markup::TAG);
});
```

#### The Extension System

The event examples above are the raw seams. When you're integrating an entire platform or plugin — routing several pages through custom queries *and* wiring up listeners — the plugin offers a tidier way to bundle it all into a single class: an **extension**. This is exactly how the plugin's own built-in [WooCommerce](https://github.com/x3p0-dev/x3p0-breadcrumbs/tree/master/src/Extension/WooCommerce) and [Sensei LMS](https://github.com/x3p0-dev/x3p0-breadcrumbs/tree/master/src/Extension/SenseiLms) integrations work, and third parties use the same mechanism with no core edits.

An extension extends `X3P0\Breadcrumbs\Extension\Extension`, which implements the event package's `ListenerSubscriber` contract, and defines one method:

- **`subscribeTo(Listenable $registry): void`** — registers the extension's listeners on the registry it is given, typically a handful of `$registry->listenTo(...)` calls. This is how you subscribe to `QueryTypeResolving`, `CrumbsBuilt`, and `MarkupRendering` without reaching for the global action bridges.

There's no `isActive()` or `register()` on the base class — an extension is treated as active the moment it's tagged (see below), so the platform guard (checking for a class or function the target platform defines) belongs in the code doing the tagging. And there's nothing to register: any custom `Query`, `Assembler`, or `Crumb` types the extension needs are just classes dispatched by their `::class` name, exactly as described above.

Constructor dependencies, if any, are resolved from the container.

Here's an extension that reroutes a page to a custom query and relabels a crumb:

```php
use X3P0\Breadcrumbs\Crumb\Crumb;
use X3P0\Breadcrumbs\Crumb\Event\CrumbsBuilt;
use X3P0\Breadcrumbs\Crumb\Type\PostType;
use X3P0\Breadcrumbs\Extension\Extension;
use X3P0\Breadcrumbs\Packages\Event\Listener\Listenable;
use X3P0\Breadcrumbs\Query\Event\QueryTypeResolving;

final class MyExtension extends Extension
{
	public function subscribeTo(Listenable $registry): void
	{
		$registry->listenTo($this->onQueryTypeResolving(...));
		$registry->listenTo($this->onCrumbsBuilt(...));
	}

	public function onQueryTypeResolving(QueryTypeResolving $event): void
	{
		if (my_platform_is_thing_page()) {
			$event->queryType = MyQuery::class;
			$event->stopPropagation();
		}
	}

	public function onCrumbsBuilt(CrumbsBuilt $event): void
	{
		$event->crumbs->replaceInstanceWhere(
			PostType::class,
			fn (PostType $crumb) => 'thing' === $crumb->postType->name,
			fn (PostType $crumb) => $event->makeCrumb(ThingCrumb::class, [
				'decoratedCrumb' => $crumb
			])
		);
	}
}
```

To activate it, guard on the target platform, bind the extension in the container, and tag it with `Extension::TAG` on the `x3p0/breadcrumbs/register` action. Tagged extensions are subscribed automatically, right alongside the built-ins:

```php
use X3P0\Breadcrumbs\Extension\Extension;

add_action('x3p0/breadcrumbs/register', function ($plugin) {
	if (! function_exists('my_platform')) {
		return;
	}

	$plugin->container()->singleton(MyExtension::class);
	$plugin->container()->tag(MyExtension::class, Extension::TAG);
});
```

That's the whole lifecycle in one class: the platform guard gating the single tag call, and the event listeners doing the real work — opted into the same boot sequence the plugin uses for its own WooCommerce and Sensei LMS integrations.

## License

X3P0 Breadcrumbs is licensed under the GPL version 3.0 or later.

The project includes resources from [Material Icons](https://fonts.google.com/icons), which are licensed under [Apache 2.0](http://www.apache.org/licenses/LICENSE-2.0.txt).
