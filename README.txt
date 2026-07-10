=== X3P0: Breadcrumbs ===

Contributors: greenshady
Donate link: http://a.co/02ggsr2
Tags: breadcrumbs, navigation, block, seo, trail
Requires at least: 7.0
Tested up to: 7.0
Requires PHP: 8.1
Stable tag: 4.1.0
License: GPL-3.0-or-later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Add breadcrumb navigation to any site. Works with block and classic themes. Auto-detects site structure and custom post types. Improves SEO.

== Description ==

X3P0: Breadcrumbs is the most robust breadcrumb navigation system for WordPress. Built on 17+ years of development, it automatically detects your permalink structure and displays accurate breadcrumbs for every page on your site.

Perfect for improving site navigation and SEO, this breadcrumbs plugin works seamlessly with both block themes and classic themes. Simply insert the Breadcrumbs block via the WordPress Site Editor or add it to your theme templates.

= Why Choose X3P0 Over the Built-in Breadcrumbs Block? =

WordPress ships with its own Breadcrumbs block, and it's a perfectly good choice for a simple, plain-text trail. But it was built to cover the basics: a text separator, a few visibility toggles, and no structured data. The moment breadcrumbs become part of your SEO strategy—or your site relies on custom post types, custom taxonomies, or date-based archives—you run into its limits.

X3P0: Breadcrumbs was built for exactly those sites. Here's what you get that the built-in block doesn't offer.

**Benefits for site owners and editors:**

- **Rich results for SEO** – Outputs Schema.org structured data as microdata, RDFa, or JSON-LD, so search engines can display rich breadcrumb snippets. The built-in block outputs no structured data at all.
- **Icon separators** – Choose an SVG icon (slash, arrow, chevron, bullet, and more), plus an optional home icon, instead of a plain text character.
- **Editable labels, no code** – Change the Home, Search, 404, and other labels right in the block settings, and edit the Home label directly on the canvas.
- **Complete date and time archives** – Adds hour, minute, second, and week trails on top of the usual year, month, and day.
- **Smarter post relationships** – Pick exactly which taxonomy appears in the trail for each post type, and map your permalink rewrite tags (`%category%`, `%author%`, date tags, and more) automatically.
- **Multisite network crumb** – Optionally link back to the network home on multisite installs.
- **Full control of the current page** – Show it, hide it, or link it.

**Benefits for developers:**

- **A true extension platform** – A dependency-injection container, typed events, and registries let you plug in custom query, assembler, crumb, and markup classes—far beyond the built-in block's single filter.
- **Multiple output formats from one call** – Render breadcrumbs as HTML, microdata, RDFa, or JSON-LD through the `breadcrumbs()` helper function.
- **Classic theme and page builder friendly** – Output breadcrumbs anywhere with a one-line `do_blocks()` call.
- **17+ years of refinement** – Mature and battle-tested, continuously developed since 2009.

Both handle the everyday cases—page hierarchies, category trails, author and search pages, 404s, and custom post type archives—so if that's all you need, the built-in block is a fine option. X3P0 is for when you need to go further.

= How to Add Breadcrumbs to Your Site =

**For Block Themes:**

1. Go to **Appearance → Editor**
2. Open your Header template part (or any template where you want breadcrumbs)
3. Click the block inserter (+) and search for "Breadcrumbs"
4. Insert the block and customize settings in the block sidebar
5. Save your template

The breadcrumbs will automatically display site-wide based on your permalink structure.

**For Classic Themes:**

Add this code to your `header.php` or any template file where you want breadcrumbs to appear:

 	<?= do_blocks('<!-- wp:x3p0/breadcrumbs /-->') ?>

= SEO Benefits =

Breadcrumbs improve your site's search engine optimization by:

- Providing clear site structure signals to search engines
- Reducing bounce rates by offering easy navigation
- Supporting Schema.org structured data markup
- Creating internal linking structure automatically
- Improving user experience and site usability

Search engines like Google display breadcrumbs in search results when properly formatted, giving your site more visibility and higher click-through rates.

= Supported Content Types =

The breadcrumbs plugin automatically handles:

- Pages and subpages (hierarchical structure)
- Blog posts with category breadcrumbs
- Custom post types (products, portfolios, etc.)
- Custom taxonomies (tags, genres, locations, etc.)
- Archive pages (date, author, taxonomy archives)
- Search results pages
- 404 error pages
- Attachment pages

= Customization Options =

**Block Settings:**

- Show/hide on the homepage
- Show/hide first crumb (home link)
- Show/hide last crumb (current page)
- Link the last crumb (current page)
- Choose separator style (e.g., slash, arrow, greater-than, bullet, chevron)
- Customize labels and text
- Determine whether to automatically detect permalink structure
- Choose taxonomies to match post types

**Developer Options:**

- Custom query classes for unique breadcrumb logic
- Custom assembler classes for conditional breadcrumb display
- Custom crumb classes for specialized breadcrumb items
- Filter hooks for modifying which breadcrumbs are shown

= How It Works =

This breadcrumbs plugin analyzes the current WordPress query and generates breadcrumbs based on:

1. **Your permalink structure** – Date-based, post name, category-based, or custom
2. **Content hierarchy** – Parent pages, category relationships, custom taxonomies
3. **Post type configuration** – Custom post type archives and relationships
4. **Taxonomy settings** – Primary categories and taxonomy hierarchies

The plugin automatically detects these settings and builds the most accurate breadcrumb trail for each page, so you don't need to configure complex rules.

= Developer Documentation =

For developers who want to customize breadcrumbs programmatically, the plugin provides extensive PHP classes and hooks. See the [GitHub repository](https://github.com/x3p0-dev/x3p0-breadcrumbs) for full developer documentation.

== Frequently Asked Questions ==

= How do I add breadcrumbs to my site? =

For block themes, open the Site Editor (**Appearance → Editor**), edit your Header template part (or any template), and insert the Breadcrumbs block. For classic themes, add `<?= do_blocks('<!-- wp:x3p0/breadcrumbs /-->') ?>` to a template file such as `header.php`. The block also works in any page builder that supports WordPress blocks, and you can place it in specific templates to control exactly where breadcrumbs appear.

= Does the plugin help with SEO? =

Yes. Breadcrumbs give search engines clear site-structure signals and add internal links, and they can appear in Google's search results for better click-through rates. The plugin can also output Schema.org structured data in microdata, RDFa, or JSON-LD format, which is what search engines read to display rich breadcrumb snippets.

= Does it work with custom post types and date-based permalinks? =

Yes. The plugin automatically detects your custom post types, taxonomies, and permalink structure—no configuration required. Date-based permalinks (e.g., `/2025/10/post-name/`) produce a Home → Year → Month → Day → Post trail, and custom post type archives are linked automatically.

= How do I show category (or other taxonomy) breadcrumbs for posts? =

In the block settings, open the **Post Taxonomies** panel and choose a taxonomy for the post type you want. This works for the built-in Post type and for any custom post type and taxonomy registered on your site.

= How do I customize the separator, labels, and icons? =

In the block settings, choose a separator or home icon (slash, arrow, chevron, bullet, and more), and open the **Labels** panel to change common labels such as Home, Search, and 404. The Home label can also be edited directly on the canvas.

= How do I control which breadcrumbs are shown? =

The block settings let you show or hide breadcrumbs on the homepage, show or hide the first (home) crumb, show or hide the last (current page) crumb, and optionally turn the current page into a link.

= Can I style the breadcrumbs with CSS? =

Yes. The breadcrumbs output standard HTML with CSS classes you can target. You can also style them with the design tools in the Site Editor or manually via `theme.json`.

= Is the plugin translation-ready? =

Yes. The plugin is fully internationalized and translated into multiple languages. All labels and text can be translated with standard WordPress translation tools.

= My breadcrumbs aren't showing correctly. How do I fix it? =

Check that the block is inserted in your template, that **Show on homepage** is enabled if you're testing on the homepage, and that your theme's CSS isn't hiding them. If parent pages are missing, confirm the pages are set as parent/child in the page editor—the plugin reads the hierarchy from WordPress. Finally, check your error log for PHP errors.

= Can developers customize breadcrumb output? =

Yes. The plugin is built on an object-oriented PHP foundation with a public API, hooks, and registries for custom query, assembler, crumb, and markup classes. See the [GitHub repository](https://github.com/x3p0-dev/x3p0-breadcrumbs) for full developer documentation.

== Screenshots ==

1. Breadcrumbs block in the Site Editor.
2. Home icon picker.
3. Separator icon picker.

== Changelog ==

= 4.1.0 =

**Added**

- New `CrumbCollection::hasWhere()` method, plus per-type "crumb exists" checks (`Post::postCrumbExists()`, `PostType::postTypeCrumbExists()`, and `Term::termCrumbExists()`) for developers extending the plugin.
- More robust `Path` assembler that checks the path a segment at a time, which is more accurate when given a full path.

**Changed**

- Framework code moved to a separately-developed `packages` folder, bundled during development via a new `Prelude` class.
- The block's `render()` method now uses a standardized way of passing parameters, so a single instance can be reused for multiple renders.
- Hook callbacks are now private class methods using PHP's first-class callable syntax.
- Now uses the default `blocks-manifest.json`.
- `Crumb` class properties are now public so a collection can be checked for a particular crumb.
- General code cleanup and improved type hinting.

**Fixed**

- Cleaner handling of the default `mapRewriteTags` attribute, now merging with the `block.json` value instead of overwriting it.

= 4.0.0 =

**Added**

- **Rewrite Tags** block option, which lets you select which post types map to rewrite tags (e.g., `%category%`, `%author%`, etc.). Only post types with rewrite tags in their slugs appear as options.
- **Post Taxonomies** block option, which lets you choose which taxonomy's terms to display in the breadcrumb trail for single post views.
- **Labels** block option, which lets you customize a subset of labels that most commonly need to be changed:
  - Home
  - Search Results
  - 404
- The Home label can also be edited directly from the content canvas.
- Content-only editing support for the block. When enabled, the toolbar controls no longer appear. However, users can edit the Home label directly in the editor canvas.
- Block supports:
  - `ariaLabel`: WordPress doesn't display a UI control for this, but it's possible to change the default `Breadcrumbs` label via the Code Editor view.
  - `shadow`: you can now add shadows—not sure how I missed adding this before.
- Time-based breadcrumbs:
  - Hour, minute, and second archive breadcrumbs.
  - Posts with hour, minute, and seconds in their permalink structure now show those crumbs (assuming rewrite mapping is enabled).
- A `JsonLinkedData` markup class for outputting JSON-LD breadcrumbs in the site head. This is not output by default, but developers can opt in.
- Action hooks:
  - `x3p0/breadcrumbs/init` - Fires just before the plugin's default service providers are registered.
  - `x3p0/breadcrumbs/register` - Fires just after the plugin's default service providers are registered.
  - `x3p0/breadcrumbs/boot` - Fires just after the plugin's default services providers have been booted.
- Filter hooks:
  - `x3p0/breadcrumbs/resolve/query-type` - Allows filtering the primary query type used to determine the breadcrumbs for the current page.
- `namespace` argument for the markup configuration, which allows controlling the HTML class prefixes or each element (defaults to `breadcrumbs`).

**Changed**

- A complete overhaul of the public-facing developer API. The plugin is no longer compatible with classes/functions earlier than 4.0.0 for people who were extending it with PHP.
- Because the new Rewrite Tags and Post Taxonomies block options were added, the block no longer defaults to display a category for posts. This can be set manually by the user, within patterns, or within theme templates.
- Under the hood, for the same reason as above, the PHP code no longer auto-sets the category taxonomy for posts when the permalink structure is set to `%postname%`. This was a relic from the code when users didn't have direct control over the settings.
- All block panels now use the Tools Panel component, which brings them up to date with the Core blocks in WordPress 6.9.
- The icon-based block attributes have been merged into single attributes. The block is still backwards compatible with the old attributes with no current plans for removing their support:
  - `separator` and `separatorType` → `separatorIcon`
  - `homePrefix` and `homePrefixType`→ `homeIcon`
- The block preview in the content canvas now shows Home → Ancestor → Parent → Current. This change was implemented so that users can better see the effects of removing the first and/or last breadcrumbs in the editor.
- The `justifyContent` attribute is strictly limited to `left`, `center`, and `right`. These have always been the only options. It's just defined in `block.json` now.
- The `404 Not Found` label has been changed to `Page not found` to match WordPress's default output for 404 document titles.
- The plugin now uses Composer for autoloading instead of the previous custom `Autoload` class.
- All nested elements for the block now use the `.wp-block-x3p0-breadcrumbs__` prefix instead of `.breadcrumbs__`.

**Fixed**

- Post taxonomy terms on single post views are no longer sorted by the plugin. This allows third-party plugins to manage the sorting. This is particularly important for SEO plugins that allow you to set a "primary" term for a post.
- Correctly determine when a weekly archive is being displayed.

**Removed**

- Support for editing the Block layout. Previously, you could change how the block wrapped, but this was just UI clutter that served no practical purpose.
- Call to `wp_set_script_translations()` for the block. WordPress already handles this through the normal block registration process.

For complete version history, see the [changelog on GitHub](https://github.com/x3p0-dev/x3p0-breadcrumbs/blob/master/CHANGELOG.md).

== Upgrade Notice ==

= 4.0.0 =

The block now has **Post Taxonomies** and **Map Rewrite Tags** options. You may need to set these options to get the structure that you prefer.

Developers: the public API has changed. See the [GitHub README](https://github.com/x3p0-dev/x3p0-breadcrumbs/blob/master/README.md) for more info.
