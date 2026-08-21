/**
 * Post icon row component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Internal dependencies.
import { IconLibraryModal, IconPreview } from '../../blocks/breadcrumbs/components/ui';
import { META_KEYS } from '../../blocks/breadcrumbs/utils/meta-keys';

// WordPress dependencies.
import { __, sprintf } from '@wordpress/i18n';
import {
	Button,
	__experimentalHStack as HStack,
	__experimentalTruncate as Truncate
} from '@wordpress/components';
import { PluginPostStatusInfo, store as editorStore } from '@wordpress/editor';
import { store as coreStore, useEntityProp } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import { useState } from '@wordpress/element';

/**
 * Renders the post's breadcrumb icon as a row in the editor's Summary panel —
 * in the post editor, and in the site editor once it is editing a page — built
 * to read as one of the panel's own rows: a label column beside a tertiary
 * button, the way Status, Publish, and URL are laid out. Core's `PostPanelRow`
 * isn't part of the editor package's public API, so its markup is mirrored here
 * by hand — two class names and an `HStack`.
 *
 * The value is the post's own icon meta, which `Crumb\Type\Post` reads as its
 * `explicitIcon()` and so outranks every icon option the site owner configured
 * for the post type. That is what makes this an editorial choice about one post
 * rather than a setting, and why the row previews nothing when the meta is
 * empty: the icon that would render instead belongs to the post type, and
 * showing it here would read as a choice already made for this post.
 * @returns {JSX.Element|null}
 */
export const IconRow = () => {
	const [isLibraryOpen, setLibraryOpen] = useState(false);

	const {postType, postId, isViewable} = useSelect((select) => {
		const {getCurrentPostType, getCurrentPostId} = select(editorStore);
		const type = getCurrentPostType();

		return {
			postType: type,
			postId: getCurrentPostId(),
			isViewable: Boolean(type && select(coreStore).getPostType(type)?.viewable)
		};
	}, []);

	// The post is named outright rather than left to `useEntityProp` to read
	// off the entity context, because there isn't always one to read. The post
	// editor renders `PluginArea` among the editor's own children, so a fill
	// lands inside its `EntityProvider`; the site editor renders `PluginArea`
	// as a sibling of the whole layout, so a fill there sits outside every
	// provider and the context id comes back undefined. What the editor store
	// reports is true from anywhere in either one.
	const [meta, setMeta] = useEntityProp('postType', postType, 'meta', postId);

	const value = meta?.[META_KEYS.icon] ?? '';

	// The chosen icon's own record, for the preview beside the button's label
	// and for the name the button reads out. Nothing is requested until the
	// post has an icon to resolve.
	const icon = useSelect(
		(select) => value
			? select(coreStore).getEntityRecord('root', 'icon', value)
			: null,
		[value]
	);

	// Whether there is a row to show is settled here rather than in PHP, since
	// the Summary panel belongs to the editor rather than to a screen: the post
	// editor has it, and so does the site editor once it is editing a page. But
	// the site editor edits templates, template parts, and synced patterns in
	// that same frame, and `viewable` is what tells a post type with a URL — and
	// so a place in a trail — apart from those. The meta alone wouldn't:
	// `wp_block` supports custom fields without ever appearing in a trail.
	//
	// The meta covers the other half. A post type that doesn't support custom
	// fields keeps it out of the REST response entirely, leaving nothing here to
	// read or write however the meta itself was registered.
	if (! META_KEYS.icon || ! isViewable || ! meta) {
		return null;
	}

	const setIcon = (next) => {
		setMeta({...meta, [META_KEYS.icon]: next});
		setLibraryOpen(false);
	};

	// The icon's registered name, standing in with the stored reference until
	// the record resolves — and for good if it never does, which says plainly
	// that the post is holding an icon nothing is registered under anymore.
	const label = value
		? (icon?.label ?? value)
		: __('Add an icon', 'x3p0-breadcrumbs');

	return (
		<PluginPostStatusInfo className="x3p0-breadcrumbs-post-icon">
			<HStack className="editor-post-panel__row">
				<div className="editor-post-panel__row-label">
					{__('Icon', 'x3p0-breadcrumbs')}
				</div>
				<div className="editor-post-panel__row-control">
					<Button
						className="x3p0-breadcrumbs-post-icon__toggle"
						variant="tertiary"
						size="compact"
						icon={icon?.content && <IconPreview content={icon.content}/>}
						onClick={() => setLibraryOpen(true)}
						aria-label={value
							? sprintf(
								// translators: %s: Name of the post's current breadcrumb icon.
								__('Change icon: %s', 'x3p0-breadcrumbs'),
								label
							)
							: undefined}
					>
						<Truncate numberOfLines={1}>
							{label}
						</Truncate>
					</Button>
				</div>
			</HStack>
			{isLibraryOpen && (
				<IconLibraryModal
					value={value}
					title={__('Breadcrumb Icon', 'x3p0-breadcrumbs')}
					description={__('Pick an icon for this breadcrumb item.', 'x3p0-breadcrumbs')}
					onSelect={setIcon}
					onReset={() => setIcon('')}
					onRequestClose={() => setLibraryOpen(false)}
				/>
			)}
		</PluginPostStatusInfo>
	);
};
