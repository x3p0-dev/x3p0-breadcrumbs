/**
 * Settings panel component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// WordPress dependencies.
import { getBlockType } from '@wordpress/blocks';
import { useInstanceId } from '@wordpress/compose';
import { useMemo } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

import {
	__experimentalToolsPanel as ToolsPanel,
	__experimentalToolsPanelItem as ToolsPanelItem,
	CustomSelectControl,
	ToggleControl
} from '@wordpress/components';

// Markup options are defined once in PHP via `MarkupType` and passed in on the
// `x3p0Breadcrumbs` global, so the editor never recreates the list. Labels
// arrive pre-translated from the server.
//
// noinspection JSUnresolvedVariable
const MARKUP_OPTIONS = window.x3p0Breadcrumbs?.markupTypes ?? [];

/**
 * Renders a `<ToolsPanel>` component with the block's primary setting controls.
 * @param props
 * @returns {JSX.Element}
 */
const SettingsPanel = ({
	attributes: {
		linkTrailEnd,
		markup,
		showOnHomepage,
		showTrailStart,
		showTrailEnd,
		showTrailingSeparator
	},
	setAttributes
}) => {
	const panelId = useInstanceId(SettingsPanel);

	// Prefer the (possibly filtered) PHP-supplied default, which should be
	// set for the block metadata; fall back to a literal as a last resort.
	const defaultMarkup = useMemo(
		() => getBlockType('x3p0/breadcrumbs')?.attributes?.markup?.default ?? 'rdfa',
		[]
	);

	return (
		<ToolsPanel
			label={__('Settings', 'x3p0-breadcrumbs')}
			resetAll={() => setAttributes({
				markup: defaultMarkup,
				showOnHomepage: false,
				showTrailStart: false,
				showTrailEnd: false,
				linkTrailEnd: false,
				showTrailingSeparator: false
			})}
			panelId={panelId}
		>
			<ToolsPanelItem
				label={__('Markup style', 'x3p0-breadcrumbs')}
				hasValue={() => markup !== defaultMarkup}
				onDeselect={() => setAttributes({ markup: defaultMarkup })}
				panelId={panelId}
				isShownByDefault={true}
			>
				<CustomSelectControl
					label={ __('Markup style', 'x3p0-breadcrumbs') }
					options={MARKUP_OPTIONS}
					value={MARKUP_OPTIONS.find(
						(option) => option.key === markup
					)}
					onChange={({ selectedItem }) => setAttributes({
						markup: selectedItem.key
					})}
					__next40pxDefaultSize={true}
				/>
			</ToolsPanelItem>
			<ToolsPanelItem
				label={__('Show on homepage', 'x3p0-breadcrumbs')}
				hasValue={() => !! showOnHomepage}
				onDeselect={() => setAttributes({ showOnHomepage: false })}
				panelId={panelId}
				isShownByDefault={true}
			>
				<ToggleControl
					label={__('Show on homepage', 'x3p0-breadcrumbs')}
					checked={showOnHomepage}
					onChange={() => setAttributes({
						showOnHomepage: ! showOnHomepage
					})}
					__nextHasNoMarginBottom={true}
				/>
			</ToolsPanelItem>
			<ToolsPanelItem
				label={__('Show home breadcrumb', 'x3p0-breadcrumbs')}
				hasValue={() => !! showTrailStart}
				onDeselect={() => setAttributes({ showTrailStart: false })}
				panelId={panelId}
			>
				<ToggleControl
					label={__('Show home breadcrumb', 'x3p0-breadcrumbs')}
					checked={showTrailStart}
					onChange={() => setAttributes({
						homeIcon:       '',
						showHomeLabel:  true,
						showTrailStart: ! showTrailStart
					})}
					__nextHasNoMarginBottom={true}
				/>
			</ToolsPanelItem>
			<ToolsPanelItem
				label={__('Show current breadcrumb', 'x3p0-breadcrumbs')}
				hasValue={() => !! showTrailEnd}
				onDeselect={() => setAttributes({ showTrailEnd: false })}
				panelId={panelId}
			>
				<ToggleControl
					label={__('Show current breadcrumb', 'x3p0-breadcrumbs')}
					checked={showTrailEnd }
					onChange={() => setAttributes({
						showTrailEnd: ! showTrailEnd
					})}
					__nextHasNoMarginBottom={true}
				/>
			</ToolsPanelItem>
			{showTrailEnd && (
				<ToolsPanelItem
					label={__('Link current breadcrumb', 'x3p0-breadcrumbs')}
					hasValue={() => !! linkTrailEnd}
					onDeselect={() => setAttributes({
						linkTrailEnd: false
					})}
					panelId={panelId}
				>
					<ToggleControl
						label={__('Link current breadcrumb', 'x3p0-breadcrumbs')}
						checked={linkTrailEnd}
						onChange={() => setAttributes({
							linkTrailEnd: ! linkTrailEnd
						})}
						__nextHasNoMarginBottom={true}
					/>
				</ToolsPanelItem>
			)}
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
		</ToolsPanel>
	);
};

export default SettingsPanel;
