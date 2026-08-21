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
	BaseControl,
	CustomSelectControl,
	ToggleControl
} from '@wordpress/components';

// Shared by every icon row below purely for CSS purposes: it lets adjacent
// rows present as one flush block (see `_index.scss`), the way WordPress
// presents its own Global Styles "Elements" list.
const ROW_CLASS_NAME = 'x3p0-breadcrumbs-icon-control-item';

// Icon visibility options are defined once in PHP via `IconVisibility` and
// passed in on the `x3p0Breadcrumbs` global, so the editor never recreates the
// list. Labels arrive pre-translated from the server. The matching label
// visibility select lives in `SettingsPanel`.
//
// noinspection JSUnresolvedVariable
const ICON_VISIBILITY_OPTIONS = window.x3p0Breadcrumbs?.iconVisibilityOptions ?? [];

// The options that always have a row, listed ahead of every other one: the
// separator, which renders on every trail independently of the crumb icon
// settings, and the home crumb, which all but the most unusual trails open
// with. Every other option is added on demand from `IconOptionPicker`, so the
// panel's length tracks what the user actually configured rather than how many
// options happen to be registered.
const PINNED_KEYS = [ICON_OPTION_KEYS.SEPARATOR, ICON_OPTION_KEYS.HOME];

/**
 * Renders a `<ToolsPanel>` component with the block's icon controls: the
 * switches governing whether crumb icons and the separator render at all,
 * then one row per icon option the user is configuring, all reading and
 * writing the block's single `icons` map keyed by option key. Those switches
 * live here rather than in `SettingsPanel` because each one gates rows in
 * this panel, and a row has no business vanishing because of a control the
 * user cannot see. The matching label visibility select is the exception: it
 * gates nothing here, so it sits with the rest of the trail's text settings
 * and explains its dependency on icon visibility in its own help text.
 * @param props
 * @returns {JSX.Element}
 */
const IconsPanel = (props) => {
	const {
		attributes: {
			icons = {},
			iconVisibility,
			showSeparator = true,
			showTrailingSeparator
		},
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
			// A row only earns its place when the icon it configures can
			// actually render: crumb icon rows are hidden while icon
			// visibility is "none", and the separator row — which isn't a
			// crumb icon and is governed separately — while the separator
			// itself is turned off.
			(option) => ICON_OPTION_KEYS.SEPARATOR === option.key
				? showSeparator
				: ! iconsHidden
		);
	}, [icons, addedKeys, iconsHidden, showSeparator]);

	// Everything left for `IconOptionPicker` to offer. Pinned options are
	// never on offer even when the filter above took their row away: their
	// row is gated on a setting, not on being added, so re-adding it would
	// do nothing.
	const available = useMemo(() => {
		const shown = new Set([...PINNED_KEYS, ...rows.map((option) => option.key)]);

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

	// Puts an option's icon back to its registered default while keeping the
	// row — what the library modal's own "Reset Icon" does, and what a pinned
	// row's "-" button does. Resetting the icon says nothing about whether
	// the row is still wanted, and the user may well pick again in a moment,
	// so the row is held open for the rest of the session, which takes
	// remembering it: without an override it is no longer implied by the
	// `icons` map. Pinned rows are permanent already and stay out of
	// `addedKeys`, since nothing there needs holding open.
	const onIconReset = (key) => {
		onIconChange(key, '');

		if (! PINNED_KEYS.includes(key)) {
			setAddedKeys((keys) => keys.includes(key) ? keys : [...keys, key]);
		}
	};

	// Takes a row out of the panel, clearing any override along with it —
	// otherwise the row would come straight back, since a key in `icons` is
	// what puts one there. Only offered for rows that were added on demand;
	// pinned rows have nowhere to go.
	const onIconRemove = (key) => {
		onIconChange(key, '');
		setAddedKeys((keys) => keys.filter((added) => key !== added));
	};

	return (
		<ToolsPanel
			label={__('Icons', 'x3p0-breadcrumbs')}
			resetAll={() => {
				setAttributes({
					icons: undefined,
					iconVisibility: defaultIconVisibility,
					showSeparator: true,
					showTrailingSeparator: false
				});
				setAddedKeys([]);
			}}
			panelId={panelId}
		>
			<ToolsPanelItem
				label={__('Icon visibility', 'x3p0-breadcrumbs')}
				hasValue={() => iconVisibility !== defaultIconVisibility}
				onDeselect={() => setAttributes({ iconVisibility: defaultIconVisibility })}
				panelId={panelId}
				isShownByDefault
			>
				<BaseControl
					help={__('Which breadcrumbs show their own icon. The separator icon is not affected.', 'x3p0-breadcrumbs')}
				>
					<CustomSelectControl
						label={__('Icon visibility', 'x3p0-breadcrumbs')}
						options={ICON_VISIBILITY_OPTIONS}
						value={ICON_VISIBILITY_OPTIONS.find(
							(option) => option.key === iconVisibility
						)}
						onChange={({ selectedItem }) => setAttributes({
							iconVisibility: selectedItem.key
						})}
					/>
				</BaseControl>
			</ToolsPanelItem>
			<ToolsPanelItem
				label={__('Show separator', 'x3p0-breadcrumbs')}
				hasValue={() => ! showSeparator}
				onDeselect={() => setAttributes({ showSeparator: true })}
				panelId={panelId}
			>
				<ToggleControl
					label={__('Show separator', 'x3p0-breadcrumbs')}
					checked={showSeparator}
					onChange={() => setAttributes({
						showSeparator: ! showSeparator
					})}
					__nextHasNoMarginBottom={true}
				/>
			</ToolsPanelItem>
			{showSeparator && (
				<ToolsPanelItem
					label={__('Show trailing separator', 'x3p0-breadcrumbs')}
					hasValue={() => !! showTrailingSeparator}
					onDeselect={() => setAttributes({
						showTrailingSeparator: false
					})}
					panelId={panelId}
				>
					<ToggleControl
						label={__('Show trailing separator', 'x3p0-breadcrumbs')}
						checked={showTrailingSeparator}
						onChange={() => setAttributes({
							showTrailingSeparator: ! showTrailingSeparator
						})}
						__nextHasNoMarginBottom={true}
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
								onRemove={PINNED_KEYS.includes(option.key)
									? undefined
									: () => onIconRemove(option.key)}
								defaultIcon={option.icon}
								label={option.name}
								controlIcon={fallbackControlIcon}
								openLabel={sprintf(__('Replace %s icon', 'x3p0-breadcrumbs'), option.name)}
								resetLabel={sprintf(__('Reset %s icon', 'x3p0-breadcrumbs'), option.name)}
								removeLabel={sprintf(__('Remove %s icon', 'x3p0-breadcrumbs'), option.name)}
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
