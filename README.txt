=== Breadcrumbs Block – Navigation Trail ===

Contributors: greenshady
Tags: breadcrumbs, navigation, block, seo, trail
Requires at least: 6.8
Tested up to: 6.8
Requires PHP: 8.1
Stable tag: 4.0.0
License: GPL-3.0-or-later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Add breadcrumb navigation to any site. Works with block and classic themes. Auto-detects site structure and custom post types. Improves SEO.

== Description ==

X3P0: Breadcrumbs is the most advanced breadcrumb navigation system for WordPress. Built on 15+ years of development, it automatically detects your permalink structure and displays accurate breadcrumbs for every page on your site.

Perfect for improving site navigation and SEO, this breadcrumbs plugin works seamlessly with both block themes and classic themes. Simply insert the Breadcrumbs block via the WordPress Site Editor or add it to your theme templates.

= Key Features =

**Automatic Site Structure Detection**

The plugin automatically detects your permalink setup and builds breadcrumbs based on your site's hierarchy. No configuration needed – it just works out of the box. But you can also customize if you need to change it.

**Custom Post Type Support**

Automatically recognizes custom post types and custom taxonomies created by other plugins or your theme. Whether you're running an ecommerce solution, a portfolio site, or any custom setup, breadcrumbs will display correctly.

**Block Theme and Classic Theme Compatible**

Insert breadcrumbs using the WordPress block editor in any template, template part, or pattern. For classic themes, easily add breadcrumbs using a simple PHP function call.

**Advanced Developer Customization**

Built on a robust object-oriented PHP foundation with extensive hooks and filters. Developers can customize every aspect of breadcrumb generation and display.

**Schema.org Structured Data Support**

Choose from plain HTML, Schema.org microdata, or RDFa formats for optimal SEO and search engine visibility.

**Flexible Display Options**

Control which breadcrumb items display, customize separators, hide breadcrumbs on specific pages, and style breadcrumbs to match your theme.

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

- Show/hide on front page
- Show/hide first item (home link)
- Show/hide last item (current page)
- Choose separator style (slash, arrow, greater-than, bullet, chevron)
- Customize labels and text
- Determine whether to automatically detect permalink structure
- Choose taxonomies to match post types

**Developer Options:**

- Custom query classes for unique breadcrumb logic
- Custom assembler classes for conditional breadcrumb display
- Custom crumb classes for specialized breadcrumb items
- Filter hooks for modifying builder configuration
- Filter hooks for modifying markup options

= How It Works =

This breadcrumbs plugin analyzes the current WordPress query and generates breadcrumbs based on:

1. **Your permalink structure** – Date-based, post name, category-based, or custom
2. **Content hierarchy** – Parent pages, category relationships, custom taxonomies
3. **Post type configuration** – Custom post type archives and relationships
4. **Taxonomy settings** – Primary categories and taxonomy hierarchies

The plugin automatically detects these settings and builds the most accurate breadcrumb trail for each page, so you don't need to configure complex rules.

= Developer Documentation =

For developers who want to customize breadcrumbs programmatically, the plugin provides extensive PHP classes and hooks. Read the [GitHub repository](https://github.com/x3p0-dev/x3p0-breadcrumbs) for full developer documentation.

= Why Choose X3P0: Breadcrumbs? =

Unlike simpler breadcrumb plugins, X3P0: Breadcrumbs handles complex site structures automatically:

- **Hierarchical pages** – Automatically shows parent → child relationships
- **Category-based permalinks** – Shows category structure in URLs
- **Date-based archives** – Displays year/month/day breadcrumb trails
- **Custom post type archives** – Links back to custom post type archive pages
- **Taxonomy hierarchies** – Displays parent/child category relationships
- **Multisite networks** – Can include network home in breadcrumb trail

The plugin has been refined since 2009, making it one of the most mature and reliable breadcrumb solutions for WordPress.

== Frequently Asked Questions ==

= How do I add breadcrumbs to my WordPress site? =

For block themes, open the Site Editor (**Appearance → Editor**), edit your Header template part, and insert the Breadcrumbs block.

For classic themes, add `<?= do_blocks('<!-- wp:x3p0/breadcrumbs /-->') ?>` to your template files.

= Do breadcrumbs help with SEO? =

Yes. Breadcrumbs improve SEO by providing clear site structure to search engines, reducing bounce rates, and creating internal links. They also appear in Google search results when properly formatted, improving click-through rates.

= Does this work with custom post types? =

Yes. The plugin automatically detects all custom post types and taxonomies on your site and generates appropriate breadcrumbs for them without any configuration.

= Can I customize the breadcrumb separator? =

Yes. In the block settings panel, you can choose from several separator styles including slash (/), arrow (→), greater-than (>), bullet (•), and chevron (›).

= How do I hide breadcrumbs on the homepage? =

In the block settings panel, toggle off **Show on homepage**.

= Can I show category breadcrumbs for blog posts? =

Yes. In the block settings panel, select a taxonomy under the **Post Taxonomies** panel. You can choose the taxonomy for any post types registered on your site.

= How do I add breadcrumbs to specific pages only? =

Insert the Breadcrumbs block directly into individual page templates or use conditional logic in your theme templates to control where breadcrumbs appear.

= Does the plugin support Schema.org markup? =

Yes. The plugin includes three markup options: plain HTML, Schema.org microdata, and RDFa format. Developers can specify which format to use when implementing breadcrumbs via PHP.

= How do I customize breadcrumb labels? =

In the block settings panel, select the label you want to customize under the **Labels** panel. This provides a subset of common labels that you may want to modify.

For more advanced use cases, use the `x3p0/breadcrumbs/builder/config` filter hook to modify labels for home, archives, search results, pagination, and date archives. See the developer documentation for examples.

= Why don't my breadcrumbs show parent pages? =

Make sure your pages are actually set as parent/child in the page editor. The plugin reads the page hierarchy from WordPress and displays it automatically.

= Does this work with page builders? =

Yes. You can insert the Breadcrumbs block into any page builder that supports WordPress blocks, including patterns and reusable blocks.

= How do I add custom breadcrumbs for specific pages? =

Developers can create custom query classes or use the `x3p0/breadcrumbs/builder/pre/build` filter hook to implement custom breadcrumb logic for specific conditions.

= Can I hide the current page from breadcrumbs? =

Yes. In the block settings, toggle off **Show last breadcrumb**.

= Is the plugin translation-ready? =

Yes. The plugin is fully internationalized and has been translated into multiple languages. All labels and text can be translated using standard WordPress translation tools.

= Can I style the breadcrumbs with CSS? =

Yes. The breadcrumbs output standard HTML with CSS classes you can target. You can customize it with the common design tools available in the Site Editor or manually via `theme.json`.

= Does it work with date-based permalink structures? =

Yes. If your site uses date-based permalinks (e.g., `/2025/10/post-name/`), the plugin automatically generates breadcrumbs showing Home → Year → Month → Day → Post.

= How do I display taxonomy breadcrumbs for custom post types? =

In the block settings panel, select a taxonomy under the **Post Taxonomies** panel. Choose your custom post type and its associated taxonomy.

= How do I troubleshoot breadcrumbs not appearing? =

Check that:

1. The block is inserted in your template
2. **Show on homepage** is enabled if testing on homepage
3. Your theme doesn't have CSS hiding breadcrumbs
4. There are no PHP errors in your error log

= How to use breadcrumbs  for better navigation? =

**Step 1: Choose Your Template**

Decide where breadcrumbs should appear. Most sites display them in the header, just below the site title and navigation menu, or at the top of the content area.

**Step 2: Insert the Block**

Navigate to **Appearance → Editor**, open your chosen template part, and insert the Breadcrumbs block using the block inserter.

**Step 3: Configure Settings**

Customize the block appearance using the settings panel. Choose your separator style, decide whether to show breadcrumbs on the homepage, and adjust visibility of the first and last items.

**Step 4: Test Different Pages**

Visit different page types (single posts, category archives, pages, search results) to see how breadcrumbs adapt to each context.

== Screenshots ==

1. Breadcrumbs block in the Site Editor.
2. Home icon picker.
3. Separator icon picker.

== Changelog ==

= 3.1.0 - October 5, 2025 =

**Added**

- `spacing.blockGap` support was added, which now lets theme authors and users control the spacing between breadcrumb items via the standard block gap/spacing design tools.
  - WordPress 6.9: Theme authors should switch to styling this via `styles.blocks.x3p0/breadcrumbs.spacing.blockGap` (as of the current release date, this works with the Gutenberg plugin enabled).
  - WordPress 6.8: `blockGap` styling doesn't work in `theme.json`, so this is needed in the `css` field for the block: `&.is-layout-flex { gap: var(--wp--preset--spacing--20); }`
  - The old `settings.x3p0/breadcrumbs.blockGap` configuration will still work.
  - The fallback `blockGap` style for the block is `0.5rem`.
- Horizontal flex `layout` support was added, primarily as a requirement for `blockGap` to work. This is not configurable, but it does use the WordPress layout implementation instead of custom CSS.

**Fixed**

- Corrected handling of the option for showing breadcrumbs on the front page. Previously, they did not appear if enabled.
- Editor scripts translations should now work. Previously, a call to `wp_set_script_translations()` was missing.
- The `Container` interface previously incorrectly extended the `Bootable` interface.
- Accessibility: The `aria-current` attribute is now applied to the last breadcrumb item.

**Changed**

- The plugin now only supports WordPress 6.8. Any back-compat code for older versions has been removed.
- Interactivity router region support moved to the `container_attr` array for `Markup` classes. These attributes are no longer forcefully injected and can be overwritten.
- The `breadcrumbs` value is now applied to the `data-wp-router-region` attribute.
- When filtering `x3p0/breadcrumbs/builder/pre/build` a type error will be thrown if a non-null value other than `X3P0\Breadcrumbs\Contracts\Builder` implementation is returned.

**Removed**

- The `Plugin` class was removed and renamed to `App`. This should not affect old installations since third-party devs should have been using the `plugin()` helper function.
- The `Trail` class has been removed, which was deprecated in version 2.0.0.

For complete version history, see the [changelog on GitHub](https://github.com/x3p0-dev/x3p0-breadcrumbs/blob/master/CHANGELOG.md).

== Upgrade Notice ==

= 4.0.0 =

The Breadcrumbs block now has **Post Taxonomies** and **Map Rewrite Tags** options. Therefore, you may need to manually set these options to get the structure that you prefer.
