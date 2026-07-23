/**
 * Settings panel component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// WordPress dependencies.
import { useInstanceId } from '@wordpress/compose';
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

// noinspection JSUnresolvedVariable
const DEFAULT_MARKUP = window.x3p0Breadcrumbs?.defaultMarkup ?? 'rdfa';

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

	return (
		<ToolsPanel
			label={__('Settings', 'x3p0-breadcrumbs')}
			resetAll={() => setAttributes({
				markup: DEFAULT_MARKUP,
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
				hasValue={() => markup !== DEFAULT_MARKUP}
				onDeselect={() => setAttributes({ markup: DEFAULT_MARKUP })}
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
