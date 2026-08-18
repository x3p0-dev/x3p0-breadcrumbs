/**
 * Icons panel component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Internal dependencies.
import HomeIconControl      from './HomeIconControl';
import SeparatorIconControl from './SeparatorIconControl';

// WordPress dependencies.
import { getBlockType } from '@wordpress/blocks';
import { useInstanceId } from '@wordpress/compose';
import { useMemo } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
	__experimentalToolsPanel as ToolsPanel,
	__experimentalToolsPanelItem as ToolsPanelItem
} from '@wordpress/components';

// Shared by every icon `ToolsPanelItem` below purely for CSS purposes: it
// lets adjacent items present as one flush block (see `_index.scss`) while
// remaining fully independent, separately-togglable panel items — the same
// way WordPress groups its own Background or Color panel rows.
const ITEM_CLASS_NAME = 'x3p0-breadcrumbs-icon-control-item';

/**
 * Renders a `<ToolsPanel>` component with the block's icon controls. Starts
 * with the home and separator icons; more icon settings will join as
 * additional panel items as they're added.
 * @param props
 * @returns {JSX.Element}
 */
const IconsPanel = (props) => {
	const {
		attributes: { homeIcon, separatorIcon, showTrailStart },
		setAttributes
	} = props;

	const panelId = useInstanceId(IconsPanel);

	// Prefer the (possibly filtered) PHP-supplied default, which should be
	// set for the block metadata; fall back to a literal as a last resort.
	const defaultSeparatorIcon = useMemo(
		() => getBlockType('x3p0/breadcrumbs')?.attributes?.separatorIcon?.default
			?? 'x3p0-breadcrumbs/chevron',
		[]
	);

	return (
		<ToolsPanel
			label={__('Icons', 'x3p0-breadcrumbs')}
			resetAll={() => setAttributes({
				homeIcon: '',
				separatorIcon: defaultSeparatorIcon
			})}
			panelId={panelId}
		>
			<ToolsPanelItem
				className={ITEM_CLASS_NAME}
				label={__('Separator', 'x3p0-breadcrumbs')}
				hasValue={() => separatorIcon !== defaultSeparatorIcon}
				onDeselect={() => setAttributes({ separatorIcon: defaultSeparatorIcon })}
				panelId={panelId}
				isShownByDefault
			>
				<SeparatorIconControl {...props} defaultSeparatorIcon={defaultSeparatorIcon} />
			</ToolsPanelItem>
			{showTrailStart && (
				<ToolsPanelItem
					className={ITEM_CLASS_NAME}
					label={__('Home', 'x3p0-breadcrumbs')}
					hasValue={() => !! homeIcon}
					onDeselect={() => setAttributes({ homeIcon: '' })}
					panelId={panelId}
					isShownByDefault
				>
					<HomeIconControl {...props} />
				</ToolsPanelItem>
			)}
		</ToolsPanel>
	);
};

export default IconsPanel;
