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
import IconOptionPicker     from './IconOptionPicker';
import SeparatorIconControl from './SeparatorIconControl';
import { ICON_OPTIONS, ICON_OPTION_KEYS } from '../../utils/icon-options';

// WordPress dependencies.
import { getBlockType } from '@wordpress/blocks';
import { useInstanceId } from '@wordpress/compose';
import { useMemo, useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { post as fallbackControlIcon } from '@wordpress/icons';
import {
	__experimentalToolsPanel as ToolsPanel,
	__experimentalToolsPanelItem as ToolsPanelItem,
	CustomSelectControl
} from '@wordpress/components';

// Shared by every icon row below purely for CSS purposes: it lets adjacent
// rows present as one flush block (see `_index.scss`), the way WordPress
// presents its own Global Styles "Elements" list.
const ROW_CLASS_NAME = 'x3p0-breadcrumbs-icon-control-item';

// Icon/label visibility options are defined once in PHP via `IconVisibility`
// and `LabelVisibility` and passed in on the `x3p0Breadcrumbs` global, so the
// editor never recreates either list. Labels arrive pre-translated from the
// server.
//
// noinspection JSUnresolvedVariable
const ICON_VISIBILITY_OPTIONS = window.x3p0Breadcrumbs?.iconVisibilityOptions ?? [];

// noinspection JSUnresolvedVariable
const LABEL_VISIBILITY_OPTIONS = window.x3p0Breadcrumbs?.labelVisibilityOptions ?? [];

// The options that always have a row, listed ahead of every other one: the
// separator, which renders on every trail regardless of the icon settings,
// and the home crumb, which all but the most unusual trails open with. Every
// other option is added on demand from `IconOptionPicker`, so the panel's
// length tracks what the user actually configured rather than how many
// options happen to be registered.
const PINNED_KEYS = [ICON_OPTION_KEYS.SEPARATOR, ICON_OPTION_KEYS.HOME];

/**
 * Renders a `<ToolsPanel>` component with the block's icon controls: the
 * icon/label visibility selects, then one row per icon option the user is
 * configuring, all reading and writing the block's single `icons` map keyed
 * by option key.
 * @param props
 * @returns {JSX.Element}
 */
const IconsPanel = (props) => {
	const {
		attributes: { icons = {}, iconVisibility, labelVisibility },
		setAttributes
	} = props;

	const panelId = useInstanceId(IconsPanel);

	// Options the user added a row for without picking an icon yet. A row is
	// otherwise implied by the presence of a key in `icons`, but an option
	// only lands there once it holds a real override, so a freshly added row
	// needs somewhere to live until then. Deliberately not persisted: an
	// empty row is a step in the middle of making a choice, not a choice.
	const [addedKeys, setAddedKeys] = useState([]);

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

	// The options with a row, pinned ones first and the rest in registry
	// order, so a row holds its place no matter when it was added.
	const rows = useMemo(() => {
		const shown = new Set([...PINNED_KEYS, ...Object.keys(icons), ...addedKeys]);

		return [
			...PINNED_KEYS.map(
				(key) => ICON_OPTIONS.find((option) => key === option.key)
			),
			...ICON_OPTIONS.filter(
				(option) => shown.has(option.key) && ! PINNED_KEYS.includes(option.key)
			)
		].filter(Boolean).filter(
			// Crumb icon rows are hidden while icon visibility is "none";
			// the separator row stays, since it isn't a crumb icon and
			// renders regardless.
			(option) => ICON_OPTION_KEYS.SEPARATOR === option.key || ! iconsHidden
		);
	}, [icons, addedKeys, iconsHidden]);

	// Everything left for `IconOptionPicker` to offer.
	const available = useMemo(() => {
		const shown = new Set(rows.map((option) => option.key));

		return ICON_OPTIONS.filter((option) => ! shown.has(option.key));
	}, [rows]);

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

	// Clears an option's override and, unless it's a pinned row, takes the
	// row away with it: an added row exists to hold an override, so there's
	// nothing left for it to do once that override is gone.
	const onIconReset = (key) => {
		onIconChange(key, '');
		setAddedKeys((keys) => keys.filter((added) => key !== added));
	};

	return (
		<ToolsPanel
			// Remounting when the visibility gate flips rebuilds the
			// panel's item and dropdown-menu registration from scratch, so
			// the label visibility item that unmounts can't linger as a
			// selectable entry in the panel's own menu.
			key={iconsHidden ? 'icons-hidden' : 'icons-visible'}
			label={__('Icons', 'x3p0-breadcrumbs')}
			resetAll={() => {
				setAttributes({
					icons: undefined,
					iconVisibility: defaultIconVisibility,
					labelVisibility: defaultLabelVisibility
				});
				setAddedKeys([]);
			}}
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
			{/*
				The icon rows are plain children rather than
				`ToolsPanelItem`s: the panel's own "+" menu is built for a
				fixed, curated set of controls, and the icon options are an
				open-ended list that grows with every post type and taxonomy
				registered on the site. `IconOptionPicker` takes over that
				job with a searchable, grouped menu instead. Panel children
				are laid out on a two-column grid, hence the wrapper's
				`grid-column` span in `_index.scss`.
			*/}
			<div className="x3p0-breadcrumbs-icons-panel__overrides">
				{rows.map((option) => (
					<div className={ROW_CLASS_NAME} key={option.key}>
						{ICON_OPTION_KEYS.SEPARATOR === option.key ? (
							<SeparatorIconControl
								value={icons[option.key] || ''}
								onChange={(value) => onIconChange(option.key, value)}
								defaultIcon={option.icon}
							/>
						) : (
							<IconControl
								value={icons[option.key] || ''}
								onChange={(value) => onIconChange(option.key, value)}
								onReset={() => onIconReset(option.key)}
								defaultIcon={option.icon}
								label={option.name}
								controlIcon={fallbackControlIcon}
								openLabel={sprintf(__('Replace %s icon', 'x3p0-breadcrumbs'), option.name)}
								resetLabel={sprintf(__('Remove %s icon', 'x3p0-breadcrumbs'), option.name)}
								modalTitle={sprintf(__('%s Icon', 'x3p0-breadcrumbs'), option.name)}
								modalDescription={sprintf(__('Pick an icon for the %s breadcrumb item.', 'x3p0-breadcrumbs'), option.name)}
							/>
						)}
					</div>
				))}
				{! iconsHidden && available.length > 0 && (
					<IconOptionPicker
						options={available}
						onSelect={(key) => setAddedKeys((keys) => [...keys, key])}
					/>
				)}
			</div>
		</ToolsPanel>
	);
};

export default IconsPanel;
