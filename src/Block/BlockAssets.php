<?php

/**
 * Block editor assets class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Block;

use X3P0\Breadcrumbs\Icon\IconOptionKey;
use X3P0\Breadcrumbs\Icon\IconOptionRegistry;
use X3P0\Breadcrumbs\Markup\IconVisibility;
use X3P0\Breadcrumbs\Markup\LabelVisibility;
use X3P0\Breadcrumbs\Markup\MarkupOptions;
use X3P0\Breadcrumbs\Meta\MetaKey;
use X3P0\Breadcrumbs\Packages\Framework\Contracts\Bootable;

/**
 * Passes server-side data to the block editor. The selectable markup types are
 * defined once in PHP (captured via {@see MarkupOptions}) and the icon/label
 * visibility options come from the {@see IconVisibility} and
 * {@see LabelVisibility} enums, all handed to the editor script so the
 * JavaScript never has to recreate (and risk desyncing) any of the lists. The
 * icon options — including one per viewable post type and public taxonomy,
 * enumerated by `IconOptionRegistrar` late on `init` — likewise come from the
 * {@see IconOptionRegistry} registry, so the editor's icon controls and canvas
 * preview crumbs stay in sync with the real defaults rather than recreating
 * the lookups client-side. The {@see MetaKey} cases go over as well, since the
 * canvas previews the open post's own icon on the trail's last crumb.
 */
final class BlockAssets implements Bootable
{
	/**
	 * JavaScript global that the editor data is assigned to.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const SCRIPT_GLOBAL = 'x3p0Breadcrumbs';

	/**
	 * Stores the icon and markup options passed to the editor.
	 */
	public function __construct(
		private readonly IconOptionRegistry $iconOptions,
		private readonly MarkupOptions      $markupOptions
	) {}

	/**
	 * @inheritDoc
	 */
	public function boot(): void
	{
		add_action('enqueue_block_editor_assets', $this->enqueue(...));
	}

	/**
	 * Attaches the editor data to the block's editor script. The handle is
	 * derived with `generate_block_asset_handle()` — the same function
	 * WordPress uses to build it from the block metadata — so it stays
	 * correct without hardcoding.
	 */
	private function enqueue(): void
	{
		wp_add_inline_script(
			generate_block_asset_handle(BlockRegistrar::BLOCK_NAME, 'editorScript'),
			sprintf(
				'window.%1$s = Object.assign(window.%1$s || {}, %2$s);',
				self::SCRIPT_GLOBAL,
				wp_json_encode(
					[
						'markupTypes'           => $this->markupOptions->forBlock(),
						'defaultMarkup'         => $this->markupOptions->getBlockDefaultKey(),
						'iconVisibilityOptions' => array_map(
							static fn (IconVisibility $case) => [
								'key'  => $case->value,
								'name' => $case->label()
							],
							IconVisibility::cases()
						),
						'labelVisibilityOptions' => array_map(
							static fn (LabelVisibility $case) => [
								'key'  => $case->value,
								'name' => $case->label()
							],
							LabelVisibility::cases()
						),
						'iconOptions'      => $this->iconOptions->forBlock(),
						'iconOptionGroups' => $this->iconOptions->groupsForBlock(),
						'postTypeIconKeys' => $this->postTypeIconKeys(),
						'metaKeys'         => MetaKey::forEditor()
					],
					JSON_HEX_TAG | JSON_UNESCAPED_SLASHES
				)
			),
			'before'
		);
	}

	/**
	 * Returns the icon option key registered for each viewable post type's
	 * single-post crumbs, keyed by post type name, so the block canvas can
	 * preview the icon belonging to whatever post is open in the editor.
	 *
	 * The editor is in no position to work these out for itself. {@see
	 * IconOptionKey} owns the `post-type:{slug}` scheme and is meant to stay
	 * its only home, and the `group` and `slug` an option already carries can't
	 * stand in for the key: a group is only where a control is filed and can be
	 * reassigned after registration, and a post type's archive option carries
	 * the very same slug as its singular one.
	 *
	 * Viewable is the same bar `IconOptionRegistrar` registers post type
	 * options against, so every key here has an option behind it. A post type
	 * on one side of that line and not the other costs nothing worse than a
	 * canvas crumb falling back to the generic page icon.
	 *
	 * @return array<string, string>
	 */
	private function postTypeIconKeys(): array
	{
		$types = array_filter(get_post_types([], 'objects'), 'is_post_type_viewable');

		return array_map(
			static fn ($type) => IconOptionKey::postType($type->name),
			$types
		);
	}
}
