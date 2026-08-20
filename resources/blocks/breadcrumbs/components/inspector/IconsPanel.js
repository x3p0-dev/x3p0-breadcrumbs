/**
 * Icons panel component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Internal dependencies.
import IconControl          from './IconControl';
import SeparatorIconControl from './SeparatorIconControl';

// WordPress dependencies.
import { getBlockType } from '@wordpress/blocks';
import { useInstanceId } from '@wordpress/compose';
import { useMemo } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { post as fallbackControlIcon } from '@wordpress/icons';
import {
	__experimentalToolsPanel as ToolsPanel,
	__experimentalToolsPanelItem as ToolsPanelItem,
	CustomSelectControl
} from '@wordpress/components';

// Shared by every icon `ToolsPanelItem` below purely for CSS purposes: it
// lets adjacent items present as one flush block (see `_index.scss`) while
// remaining fully independent, separately-togglable panel items — the same
// way WordPress groups its own Background or Color panel rows.
const ITEM_CLASS_NAME = 'x3p0-breadcrumbs-icon-control-item';

// Icon/label visibility options are defined once in PHP via `IconVisibility`
// and `LabelVisibility` and passed in on the `x3p0Breadcrumbs` global, so the
// editor never recreates either list. Labels arrive pre-translated from the
// server.
//
// noinspection JSUnresolvedVariable
const ICON_VISIBILITY_OPTIONS = window.x3p0Breadcrumbs?.iconVisibilityOptions ?? [];

// noinspection JSUnresolvedVariable
const LABEL_VISIBILITY_OPTIONS = window.x3p0Breadcrumbs?.labelVisibilityOptions ?? [];

// The registered icon options, as `{key, icon, name}` triples — see
// `IconOptions::forBlock()` on the PHP side. PHP enumerates everything,
// including one option per viewable post type and public taxonomy (via
// `IconOptionRegistrar`), so the editor renders one `IconControl` row per
// entry with no client-side enumeration; a newly registered option appears
// here automatically with no JS change needed.
//
// noinspection JSUnresolvedVariable
const ICON_OPTIONS = window.x3p0Breadcrumbs?.iconOptions ?? [];

// The option rows shown in the panel by default; every other row starts
// hidden behind the panel's "+" menu.
const SHOWN_BY_DEFAULT = [ 'separator', 'home', 'post-type:post', 'post-type:page' ];

/**
 * Renders a `<ToolsPanel>` component with the block's icon controls: the
 * icon/label visibility selects, the separator icon, and one row per
 * registered icon option, all reading and writing the block's single `icons`
 * map keyed by option key.
 * @param props
 * @returns {JSX.Element}
 */
const IconsPanel = (props) => {
	const {
		attributes: { icons = {}, iconVisibility, labelVisibility },
		setAttributes
	} = props;

	const panelId = useInstanceId(IconsPanel);

	// Prefer the (possibly filtered) PHP-supplied default, which should be
	// set for the block metadata; fall back to a literal as a last resort.
	const defaultIconVisibility = useMemo(
		() => getBlockType('x3p0/breadcrumbs')?.attributes?.iconVisibility?.default ?? 'none',
		[]
	);

	const defaultLabelVisibility = useMemo(
		() => getBlockType('x3p0/breadcrumbs')?.attributes?.labelVisibility?.default ?? 'all',
		[]
	);

	// Every crumb's icon is hidden while icon visibility is "none" — only
	// the separator is unaffected, since it isn't a crumb icon. An absent
	// attribute means the default applies, so it must gate the same way
	// the default value would.
	const iconsHidden = 'none' === (iconVisibility ?? defaultIconVisibility);

	// Updates a single option key's icon in the `icons` map, dropping empty
	// entries from the attribute so it only ever stores real overrides.
	const onIconChange = (key, value) => {
		const updatedIcons = {...icons};

		if (value) {
			updatedIcons[key] = value;
		} else {
			delete updatedIcons[key];
		}

		setAttributes({ icons: updatedIcons });
	};

	return (
		<ToolsPanel
			// Remounting when the visibility gate flips rebuilds the
			// panel's item and dropdown-menu registration from scratch, so
			// the crumb icon rows that unmount can't linger as selectable
			// entries in the panel's own menu.
			key={iconsHidden ? 'icons-hidden' : 'icons-visible'}
			label={__('Icons', 'x3p0-breadcrumbs')}
			resetAll={() => setAttributes({
				icons: undefined,
				iconVisibility: defaultIconVisibility,
				labelVisibility: defaultLabelVisibility
			})}
			panelId={panelId}
		>
			<ToolsPanelItem
				label={__('Icon Visibility', 'x3p0-breadcrumbs')}
				hasValue={() => iconVisibility !== defaultIconVisibility}
				onDeselect={() => setAttributes({ iconVisibility: defaultIconVisibility })}
				panelId={panelId}
				isShownByDefault
			>
				<CustomSelectControl
					label={__('Icon Visibility', 'x3p0-breadcrumbs')}
					options={ICON_VISIBILITY_OPTIONS}
					value={ICON_VISIBILITY_OPTIONS.find(
						(option) => option.key === iconVisibility
					)}
					onChange={({ selectedItem }) => setAttributes({
						iconVisibility: selectedItem.key
					})}
				/>
			</ToolsPanelItem>
			{! iconsHidden && (
				<ToolsPanelItem
					label={__('Label Visibility', 'x3p0-breadcrumbs')}
					hasValue={() => labelVisibility !== defaultLabelVisibility}
					onDeselect={() => setAttributes({ labelVisibility: defaultLabelVisibility })}
					panelId={panelId}
					isShownByDefault
				>
					<CustomSelectControl
						label={__('Label Visibility', 'x3p0-breadcrumbs')}
						options={LABEL_VISIBILITY_OPTIONS}
						value={LABEL_VISIBILITY_OPTIONS.find(
							(option) => option.key === labelVisibility
						)}
						onChange={({ selectedItem }) => setAttributes({
							labelVisibility: selectedItem.key
						})}
					/>
				</ToolsPanelItem>
			)}
			{ICON_OPTIONS.filter(
				// Crumb icon rows are hidden while icon visibility is
				// "none"; the separator row stays, since it isn't a crumb
				// icon and renders regardless.
				(option) => 'separator' === option.key || ! iconsHidden
			).map((option) => (
				<ToolsPanelItem
					key={option.key}
					className={ITEM_CLASS_NAME}
					label={option.name}
					hasValue={() => !! icons[option.key]}
					onDeselect={() => onIconChange(option.key, '')}
					panelId={panelId}
					isShownByDefault={SHOWN_BY_DEFAULT.includes(option.key)}
				>
					{'separator' === option.key ? (
						<SeparatorIconControl
							value={icons[option.key] || ''}
							onChange={(value) => onIconChange(option.key, value)}
							defaultIcon={option.icon}
						/>
					) : (
						<IconControl
							value={icons[option.key] || ''}
							onChange={(value) => onIconChange(option.key, value)}
							label={option.name}
							controlIcon={fallbackControlIcon}
							openLabel={sprintf(__('Replace %s icon', 'x3p0-breadcrumbs'), option.name)}
							resetLabel={sprintf(__('Remove %s icon', 'x3p0-breadcrumbs'), option.name)}
							modalTitle={sprintf(__('%s Icon', 'x3p0-breadcrumbs'), option.name)}
							modalDescription={sprintf(__('Pick an icon for the %s breadcrumb item.', 'x3p0-breadcrumbs'), option.name)}
						/>
					)}
				</ToolsPanelItem>
			))}
		</ToolsPanel>
	);
};

export default IconsPanel;
