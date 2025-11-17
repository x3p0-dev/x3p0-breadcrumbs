/**
 * Home control component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Internal dependencies.
import { SymbolPicker, ToolbarDropdown } from '../ui';
import { HOME_ICONS } from '../../utils/constants'

// WordPress dependencies.
import { __ } from '@wordpress/i18n';
import { home as controlIcon } from '@wordpress/icons';
import { useEffect } from '@wordpress/element';
import { Flex, ToggleControl } from '@wordpress/components';

/**
 * Renders the home icon and related controls.
 * @param props
 * @returns {JSX.Element|null}
 */
const HomeControl = ({
	attributes: {
		homeIcon,
		showHomeLabel,
		showTrailStart
	},
	setAttributes
}) => {
	useEffect(() => {
		if (! showHomeLabel && ! homeIcon) {
			setAttributes({ showHomeLabel: true });
		}
	}, [ homeIcon, showHomeLabel ]);

	if (! showTrailStart) {
		return null;
	}

	return (
		<ToolbarDropdown
			value={homeIcon}
			label={__('Home Icon', 'x3p0-breadcrumbs')}
			icon={controlIcon}
		>
			<Flex direction="column" gap="4">
				<SymbolPicker
					value={homeIcon}
					onChange={(value) => setAttributes({
						homeIcon: value
					})}
					options={HOME_ICONS}
					label={__('Home Icon', 'x3p0-breadcrumbs')}
					description={__('Pick an icon or symbol for the home breadcrumb item.', 'x3p0-breadcrumbs')}
				/>
				<ToggleControl
					label={__('Show home label', 'x3p0-breadcrumbs')}
					checked={showHomeLabel}
					onChange={() => setAttributes({
						showHomeLabel: ! showHomeLabel
					})}
					disabled={! homeIcon}
					__nextHasNoMarginBottom
				/>
			</Flex>
		</ToolbarDropdown>
	);
};

export default HomeControl;
