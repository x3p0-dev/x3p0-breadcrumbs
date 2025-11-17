/**
 * Separator control component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Internal dependencies.
import { SymbolPicker, ToolbarDropdown } from '../ui';
import { SEPARATOR_ICONS } from '../../utils/constants';

// WordPress dependencies.
import { __ } from '@wordpress/i18n';
import { next } from '@wordpress/icons';

/**
 * Renders the separator control.
 * @param props
 * @returns {JSX.Element}
 */
const SeparatorControl = ({ attributes: { separatorIcon }, setAttributes }) => (
	<ToolbarDropdown
		value={separatorIcon}
		label={__('Separator', 'x3p0-breadcrumbs')}
		icon={next}
	>
		<SymbolPicker
			value={separatorIcon}
			onChange={(value) => setAttributes({ separatorIcon: value })}
			options={SEPARATOR_ICONS}
			label={__('Separator', 'x3p0-breadcrumbs')}
			description={__('Pick an icon or symbol that sits in between and separates breadcrumb items.', 'x3p0-breadcrumbs')}
		/>
	</ToolbarDropdown>
);

export default SeparatorControl;
