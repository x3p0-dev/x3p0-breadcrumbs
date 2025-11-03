/**
 * Toolbar dropdown control.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// WordPress dependencies.
import { Dropdown, ToolbarButton } from '@wordpress/components';

export const ToolbarDropdown = ({
	value,
	label,
	icon,
	children
}) => (
	<Dropdown
		className="x3p0-breadcrumbs-toolbar-dropdown"
		contentClassName="x3p0-breadcrumbs-toolbar-dropdown__popover"
		focusOnMount
		popoverProps={ {
			headerTitle: label,
			variant: 'toolbar'
		} }
		renderToggle={ ({ isOpen, onToggle }) => (
			<ToolbarButton
				className="x3p0-breadcrumbs-toolbar-dropdown__button"
				icon={ icon }
				label={ label }
				onClick={ onToggle }
				aria-expanded={ isOpen }
				isPressed={ !! value }
			/>
		) }
		renderContent={ () => children }
	/>
);
