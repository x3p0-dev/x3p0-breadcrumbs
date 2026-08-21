# Crumb Icon Architecture

Status: **agreed direction** — the open questions below were decided on
2026-08-19; see the Decisions section. Written during the v5.0 development
cycle, where back-compat with 4.x internals is not a constraint (only saved
4.x block *content* must keep rendering/migrating).

Scope: icons paired with crumbs on output — plus, as of Decision 5, the
markup layer's own icon slots (the separator today; any future markup icon),
which resolve through the same system. The icon *asset* layer (`src/Icon/Icon`,
`IconRegistrar`, `IconResolver` — bundling SVGs, registering them with
WordPress's icon API, resolving a `{collection}/{name}` reference to markup)
is also out of scope: it works, and everything below treats the icon
reference string as an opaque value it hands off to that layer.

## Requirements

1. Every crumb renders a default icon with zero configuration.
2. Default icons are tied to crumb types. **Icon options are not** — an
   option is a free-standing, named thing that crumbs *pull from*. Several
   crumbs may share one option (`day`, `month`, `year` → `date`); an option
   may exist that no built-in crumb uses.
3. Some options surface in the block UI (Home, Archives, Date, Time…).
   Others exist only as resolvable defaults.
4. Post-type-bound and taxonomy-bound icons are part of the same system, not
   parallel ones.
5. Third-party developers can add custom icons (SVGs), custom crumbs, and
   custom block-facing icon options without ceremony — ideally one call each.
6. Modern, conventional PHP. The block builds on top of the developer API,
   never the reverse.

## Where things stand today

Four layers exist, built up over the last several commits plus the current
uncommitted work:

**Per-crumb defaults** — `Crumb::ICON` (protected const, late static
binding) with `getIcon()` / `defaultIcon()`. The class hierarchy shares
defaults: `Date` (`core/calendar`) → `Day`/`Week`/`Month`/`Year`, and
`TimeArchive` (`core/scheduled`) → `Hour`/`Minute`/`Second`. `Post`, `Term`,
and WooCommerce's `Endpoint` override `getIcon()` with richer cascades
(post/term meta → instance override → mime-type match → configured default).

**Config overrides** — `BreadcrumbsConfig` holds **three** separate maps
with three accessors and three resolution paths:

- `icons` — keyed by crumb slug (`home`, `search`, …)
- `postTypeIcons` — keyed by post type, shaped `{single?, archive?}`, with
  built-in defaults baked into the config class as a constant
- `taxonomyIcons` — keyed by taxonomy, defaults likewise baked in

**Block options feed** (uncommitted) — `CrumbIcon` backed enum (five cases
whose values are crumb slugs) + `CrumbHasIconOption` static interface +
`Crumb::TAG` container tag + `CrumbOptions::iconsForBlock()`, exposed to the
editor as `window.x3p0Breadcrumbs.crumbIcons`.

**Block** — three attributes mirroring the three config maps; `IconsPanel`
renders static rows from `crumbIcons` and dynamic rows from live
`core-data` post type/taxonomy records.

### What holds up

- The icon reference string as universal currency, resolved to markup only
  at the `Markup` layer.
- Hard defaults shared across the crumb class hierarchy (`Date` →
  day/week/month/year, `TimeArchive` → hour/minute/second). The *sharing*
  survives in the proposal below; the *mechanism* (per-class `ICON`
  constants) does not — defaults move into the options registry (Decision 4).
- Editorial meta overrides (post/term meta) beating everything.
- The UI split: a static PHP-supplied list for fixed options, live
  `core-data` enumeration for unbounded families (post types, taxonomies).
- The `{key, icon, name}` wire shape handed to the editor.

### What strains

**The option key is welded to the crumb slug.** `CrumbIcon` case values are
crumb slugs; `Crumb::getIcon()` looks up `config->getIcon($this->getSlug())`;
the config docblock defines `icons` as slug-keyed. Requirement 2 breaks this
outright: `day` must resolve through an option named `date` that is nobody's
slug. The uncommitted options feed (`CrumbIcon` + `CrumbHasIconOption` +
`Crumb::TAG` + `CrumbOptions`) was a faithful port of the Markup-options
pattern, but its central assumption — one option per crumb type, keyed by
that crumb's slug — is the thing requirement 2 removes. It should be
replaced, not extended.

**Three config maps for one concept.** A post type's icon and the Home icon
are both "user-chosen icon for a named slot"; only the key differs. Three
maps mean three accessors, three block attributes, three panel handlers, and
three places defaults live — two of them inside `BreadcrumbsConfig`, whose
own docblock says it stores caller overrides *only*.

**Core knows about WooCommerce.** `DEFAULT_POST_TYPE_ICONS` lists
`product`; `DEFAULT_TAXONOMY_ICONS` lists `product_cat`/`product_tag`. Those
belong to the WooCommerce extension, but the current shape has nowhere else
to put them.

**Two registration mechanisms** in the options feed (closed enum for
built-ins, interface + container tag for third parties) where one would do.

## Proposed architecture: named icon slots

One new concept carries the whole system: an **icon option** — a named slot
with a key, an optional translated label, and an optional default icon.
Options are the unit of configuration, of UI, and of defaults. Crumbs
*consume* options by key; they never define them.

### The value object

```php
// src/Icon/IconOption.php
final class IconOption
{
	public function __construct(
		public readonly string $key,
		public readonly string $icon = '',
		public readonly string $label = ''
	) {}

	// The namespaced key schemes live here as named constructors, so no
	// other code carries the raw `post-type:`/`taxonomy:` prefixes:
	public static function postTypeKey(string $postType): string;
	public static function postTypeArchiveKey(string $postType): string;
	public static function taxonomyKey(string $taxonomy): string;
}
```

- `key` — the config lookup key (`home`, `date`, `time`,
  `post-type:page`, `taxonomy:category`, `woocommerce-shop`).
- `label` — translated. **An option with a label appears in the block UI;
  an option without one is a pure default-carrier** (resolvable, invisible).
  This is how requirement 3's split falls out of the data instead of a flag.
- `icon` — the default rendered when the site owner hasn't chosen one, and
  the preview shown in the UI row.

It lives in `src/Icon/`, not `src/Crumb/` — options are no longer a crumb
concept (requirement 2), and the Icon subsystem is their natural home.

### The registry

```php
// src/Icon/IconOptions.php  (singleton service)
final class IconOptions
{
	/** @var array<string, IconOption> */
	private array $options = [];

	public function add(IconOption ...$options): void;   // last write wins
	public function get(string $key): ?IconOption;
	public function icon(string $key): string;           // get()?->icon ?? ''

	/** Labeled options only, as {key, icon, name}. */
	public function forBlock(): array;
}
```

One mechanism for everyone. Built-ins are seeded by `IconOptionRegistrar`, a
`Bootable` that `IconServiceProvider` boots. It hooks **very late on `init`**
(`PHP_INT_MAX`) and registers:

- The static labeled options (`home`, `date`, `time`, `author`, `search`,
  `error-404`) and the unlabeled default-carriers (`archive` — deliberately
  unlabeled for now per Decision 3, icon a placeholder — the `paged` family,
  `network`, `user`, and the core `post-type:`/`taxonomy:` seeds).
- **One labeled option per viewable post type** (`post-type:{slug}`, plus
  `post-type-archive:{slug}` when it has an archive) and **per public
  taxonomy** (`taxonomy:{slug}`), enumerated from what's actually registered
  with WordPress. Running after every post type/taxonomy exists means the
  block editor consumes the finished list instead of re-enumerating them
  client-side via `core-data`. Post types default to the Page icon
  (`x3p0-breadcrumbs/article`), archives to `core/category`, taxonomies to
  the Tag icon (`core/tag`) — for now.

Seeding **fills blanks rather than overwriting**: anything registered
earlier keeps its label and icon, so retargeting a built-in default means
registering the same key first (the same skip-existing convention the
plugin's other registrars follow). The registrar also disambiguates
duplicate labels (core `post_tag` and WooCommerce `product_tag` are both
"Tag"), since the editor's panel component tracks rows by label.

The registry — not class constants — is where per-type defaults live
(Decision 4); the registrar's seed list is their single home, carrying the
same values the retired `Crumb::ICON` constants did.

A third party needs exactly one call, on the hook the plugin already exposes:

```php
add_action('x3p0/breadcrumbs/register', function ($plugin) {
	$plugin->container->get(IconOptions::class)->add(
		new IconOption('woocommerce-shop', __('Shop', 'my-plugin'), 'core/store')
	);
});
```

Because `add()` is last-write-wins and built-ins are seeded at construction,
an extension can also *retarget a built-in default* (e.g. hand `date` a
different icon) with the same call — no separate override API.

This dissolves the uncommitted `CrumbIcon`, `CrumbHasIconOption`,
`Crumb::TAG`, and `CrumbOptions` entirely. The WooCommerce/Sensei entries in
`DEFAULT_POST_TYPE_ICONS`/`DEFAULT_TAXONOMY_ICONS` move into those
extensions' own registrations, taking the product knowledge out of core.

### One config map: `IconConfig` (Decision 6)

The caller's chosen icons live in their own config object —
`Icon\IconConfig`, a pure value object holding one
`icons: array<string, string>` map keyed by **option key** — because icons
are their own configuration domain: not tied to trail building
(`BreadcrumbsConfig`) or display flags (`MarkupConfig`), and consumed by
both pipelines. `BreadcrumbsConfig` carries no icons at all, and
`MarkupConfig` lost its `separatorIcon` scalar. The renderer accepts
`iconConfig` as its own argument alongside the other two configs and hands
it to both pipelines: crumbs receive it inside the `CrumbContext`
(Decision 7), and the markup factory passes it to `Markup`.

Every icon choice — crumb-linked or not — uses a key in the same map:

| Concern               | Key                                  |
|-----------------------|--------------------------------------|
| Separator (markup)    | `separator`                          |
| Home crumb            | `home`                               |
| Day/Week/Month/Year   | `date`                               |
| Hour/Minute/Second    | `time`                               |
| Single post of a type | `post-type:{slug}`                   |
| Post type archive     | `post-type-archive:{slug}`           |
| Term of a taxonomy    | `taxonomy:{slug}`                    |
| Extension crumb       | its own key, e.g. `woocommerce-shop` |

`:` as the namespace separator keeps keys visually distinct from icon
*references*, which use `/`.

### Crumb resolution

Each crumb names the **one** option it pulls from. The default is its slug,
so most types declare nothing; a shared-slot family overrides once on its
base class; the dynamic types compute it:

```php
// Crumb (base)
public function iconKey(): string
{
	return $this->getSlug();
}

public function getIcon(): string
{
	return $this->context->iconConfig->getIcon($this->iconKey())  // site-owner override
		?: $this->context->iconOptions->icon($this->iconKey())    // registered default
		?: 'x3p0-breadcrumbs/article';                            // requirement 1's last resort
}
```

```php
// Date:        public function iconKey(): string { return 'date'; }
// TimeArchive: public function iconKey(): string { return 'time'; }
// Post:        return IconOption::postTypeKey($this->post->post_type);
// PostType:    return IconOption::postTypeArchiveKey($this->postType->name);
// Term:        return IconOption::taxonomyKey($this->term->taxonomy);
```

**No per-class `ICON` constants** (Decision 4). A crumb type's default is
its registered option's `icon`; the constants (`Home::ICON`,
`Date::ICON`, `TimeArchive::ICON`, …), the `defaultIcon()` static, and the
late-static-binding machinery all go. The only compiled fallback left is the
single generic value on the base, for a crumb whose key matches no option
and no config entry. The `Date`/`TimeArchive` classes keep earning their
place through the shared constructor and `getUrl()` logic plus their
one-line `iconKey()`; icon sharing itself now happens through the shared
key, not inheritance.

Editorial meta overrides stay exactly where they are — `Post::getIcon()` and
`Term::getIcon()` check meta (and mime-type, and instance overrides) first,
then fall through to `parent::getIcon()`, which now does all the key-based
work they currently duplicate. Those overrides *shrink* under this model.
Extension crumbs whose defaults are constants today (WooCommerce Shop's
`core/store`, Endpoint's `core/more-vertical`) become options the extension
registers — unlabeled unless it also wants a UI row.

Wiring (Decisions 2 and 7): configs are pure configuration objects — icon
overrides live in `IconConfig`, nothing more. Every shared thing a crumb
reads — the trail config, the icon config, and the `IconOptions` registry —
is bundled into a single **`CrumbContext`** facade, built once per trail
build by `BreadcrumbsGenerator` and injected into every crumb by
`CrumbBuilder`, the same role `AssemblerContext`/`QueryContext` play for
their pipelines. A crumb constructor takes one `CrumbContext $context`
before its domain params; the base re-exposes `$this->config` from it (the
thing concrete types reach for most), while `getIcon()` reads the icon pair
through `$this->context` and does the override → registered default →
generic fallback chain itself.

### Block wiring

- **One attribute.** `icons: object` replaces `icons` + `postTypeIcons` +
  `taxonomyIcons` + `separatorIcon`. Every panel row — the separator's
  bespoke control included — writes through one `onIconChange(key, value)`
  handler.
- **All rows** — post types and taxonomies included — come from
  `IconOptions::forBlock()` via `window.x3p0Breadcrumbs.iconOptions`,
  exactly like `markupTypes`. Because `IconOptionRegistrar` enumerates post
  types/taxonomies server-side, the panel needs no `core-data` entity
  queries at all; it renders one `IconControl` row per entry, with
  `home`/`post-type:post`/`post-type:page` shown by default and the rest
  behind the panel's "+" menu.
- **Canvas previews** resolve `icons[key] → iconOptions default` from the
  same global, replacing the one-off `defaultPageIcon`/`defaultHomeIcon`
  globals.
- **Server render** collapses to passing `$attributes['icons']` straight
  through — the deprecation mapper still folds the 4.x-era attributes
  (`homeIcon` → `icons.home`, …) in both `deprecated.js` and the PHP mirror.
  The dev-cycle-only `postTypeIcons`/`taxonomyIcons` attributes were never
  released, so they get no migration.
- **Extensions** register their icon defaults in `Extension::boot()` (a new
  no-op hook the provider calls before subscribing listeners) — WooCommerce
  seeds `post-type:product`, `taxonomy:product_cat`/`product_tag`,
  `woocommerce-shop`, and `woocommerce-endpoint` there, all unlabeled.

## Alternatives considered

**Ordered multi-key cascade** (`Second` tries `time`, then `date`, then
default). Rejected for now: the UI shows a Time row *and* a Date row, and
each row's preview should be the truth about what renders. With a cascade,
setting only Date silently changes what the untouched Time row's crumbs
display. Single-key resolution keeps preview = output. The `iconKey()`
method leaves the door open — turning it into `iconKeys(): array` later is
additive.

**Options as classes discovered via container tag** (the current WIP, the
Markup pattern). Right for Markup, where the class *is* the behavior being
selected. Wrong here: an icon option is three strings of data, not behavior.
Class-per-option and static-interface-per-class are hoops (requirement 5).

**PHP attributes on crumb classes** (`#[IconOption('date', …)]`). Attribute
arguments must be constant expressions, so labels can't use `__()` — a
permanent i18n defect. Also re-welds options to crumb classes.

**Keeping post type/taxonomy icons as separate maps.** Preserves the typed
`{single, archive}` shape, but requirement 4 and the strain analysis both
point the other way: the shape bought three of everything and still needed
special-case defaults storage.

## Decisions (2026-08-19)

1. **Key scheme: approved.** `:` namespace separator with `post-type:{slug}`,
   `post-type-archive:{slug}`, `taxonomy:{slug}`. Can be reshaped later if a
   better scheme emerges.
2. **Registry access — revised later the same day: crumbs carry the
   registry, not config.** Configs are configuration objects only. `Crumb`
   takes `IconOptions` as a constructor dependency and resolves the
   override → default → fallback chain itself (the override side coming
   from `IconConfig` per Decision 6).
3. **No `archive` block option for now.** Archive views have coverage
   through the other options (`date`, `time`, `post-type-archive:*`,
   `taxonomy:*`); the generic `Archive` crumb keeps a default via an
   *unlabeled* registry entry. Its icon value is a placeholder — a
   purpose-picked icon is coming separately.
4. **No per-crumb `ICON` constants.** Crumbs need a default icon, but it
   doesn't have to come from a constant: the registry is the single home of
   per-type defaults, and the base class holds the one generic last-resort
   value. Constants, `defaultIcon()`, and the LSB machinery are removed.
5. **The separator is an icon option.** Registered as the labeled
   `separator` option (default `x3p0-breadcrumbs/chevron`, carried by the
   registry instead of `block.json`), stored in the shared `icons` map, and
   resolved by the `Markup` layer through the same
   override → default chain crumbs use. The `separatorIcon` block attribute
   is retired with migrations (JS deprecations + PHP mirror); a separator
   choice matching the default is skipped rather than stored. The bespoke
   text/emoji-capable separator control remains, now writing
   `icons.separator`. This is the seam future markup-layer icons use too.
6. **Icon choices live in their own `Icon\IconConfig`.** Icons are their own
   configuration domain, consumed by both pipelines — so neither
   `BreadcrumbsConfig` nor `MarkupConfig` carries them. The renderer takes
   `iconConfig` as its own argument; crumbs receive it via the
   `CrumbContext`, and the markup factory hands it to `Markup`.
7. **Crumbs take a single `CrumbContext`.** Instead of threading three
   dependencies (`BreadcrumbsConfig`, `IconConfig`, `IconOptions`) through
   every concrete constructor, crumbs take one context facade — the same
   pattern `AssemblerContext`/`QueryContext` already establish. Built per
   trail build by the generator, injected by `CrumbBuilder`, with the base
   class re-exposing `$this->config` so concrete types keep their direct
   label lookups.

## Decision 8 (2026-08-20): key identity is an enum

**`IconOptionKey` and `IconOptionGroup` name the closed sets; the registrar
still assembles the options.** As built, an option key was written down in
`IconOptionRegistrar` and read somewhere else entirely — a crumb's
`iconOptionKey()`, `Markup\Type\Html`'s separator, `Post`'s media and
role-page branches — with nothing linking the two. Seven keys were worse
than magic strings: `Home`, `Search`, `Error404`, `Archive`, `Custom`,
`User`, and `NetworkSite` were defined only by each crumb's `getSlug()`
literal, picked up through the `iconOptionKey()` default, and re-typed by
hand in the registrar. Renaming either side degraded silently to the
`fallback` option rather than failing.

Both enums are backed and cover only what this plugin owns. Every method
taking a key or group takes `IconOptionKey|string` /
`IconOptionGroup|string` and normalizes on the way in, exactly as
`IconOption` already did for `Icon|string` — a case for this plugin's, a raw
string for an extension's or for a key derived from a WordPress object.
`IconOption::postTypeKey()` and its siblings moved onto `IconOptionKey` as
`postType()`/`postTypeArchive()`/`taxonomy()`, so one type owns the whole key
vocabulary: closed cases plus open derivation. `Crumb::FALLBACK_OPTION` is
gone in favor of `IconOptionKey::Fallback`, and the built-in crumbs listed
above now name their case outright instead of riding the slug default — which
survives as the seam a third-party crumb registering under its slug uses
(`Extension\WooCommerce\Crumb\StorePage`).

**Deliberately not done: moving `label`, `icon`, and `group` into enum match
arms.** Tempting, since `Icon::label()` and `Support\Endpoint` already do it,
and it would buy match exhaustiveness. But it fixes nothing about the
fragility above — the definition site was never the problem — and it costs
the aligned key/icon/label table in `registerStaticOptions()`, whose docblock
argument about which options go unlabeled and why has nowhere to live once
it is split across three methods. It also cannot absorb the conditional
registration (`network-site` only on multisite). The line drawn instead:
**enums own identity, `IconOptionRegistrar` owns assembly.** Adding a
built-in option is a case plus a table row, and the compiler links them.

The editor mirrors this by hand in `utils/icon-options.js` as
`ICON_OPTION_KEYS`, since PHP enums do not cross the wire. It carries only
the keys the editor branches on — separator, home, and the page post type
behind the canvas placeholders — because a mirrored key nothing reads is a
copy that can rot unnoticed. Derived keys are spelled out whole there rather
than rebuilt from their parts, keeping the namespacing scheme PHP's alone.

## Suggested implementation order

1. `IconOption` + `IconOptions` + seeding in `IconServiceProvider`; delete
   `CrumbIcon`, `CrumbHasIconOption`, `CrumbOptions`, `Crumb::TAG`.
2. Crumb side: `iconKey()` on the base and the overrides
   (`Date`, `TimeArchive`, `Post`/`PostType`, `Term`); remove the `ICON`
   constants and `defaultIcon()`; slim the `getIcon()` overrides to
   meta-then-parent.
3. Config side: collapse to one `icons` map consulting the injected
   registry; move WooCommerce defaults (`product`, `product_cat`,
   `product_tag`, Shop, Endpoint) into the extension's own registration.
4. Block side: one attribute, key-computing dynamic rows, `iconOptions` +
   `iconDefaults` globals, deprecation remaps (JS + PHP mirror).
5. Sweep: `BlockAssets`, `render` pass-through, docs/inline docblocks.
