/**
 * Icon picker button component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// WordPress dependencies.
import {
	Button,
	Icon,
	__experimentalTruncate as Truncate
} from '@wordpress/components';
import { notAllowed, reset as resetIcon } from '@wordpress/icons';

/**
 * Renders a bordered preview button showing the currently selected icon
 * beside the control's own label (not the icon's name), opening `onOpen`
 * (typically `IconLibraryModal`) on click. Mirrors the row design WordPress
 * uses for the Background Image panel in Global Styles. While `value` is
 * empty, the core "not allowed" icon stands in for the preview.
 *
 * The reset button rides on `onReset` alone rather than on there being a
 * value to clear: a caller may have something to reset beyond the value
 * itself — `IconControl` uses it to take the whole row out of the panel —
 * and is the only one in a position to know. Callers with nothing to reset
 * pass no handler.
 * @param props
 * @returns {JSX.Element}
 */
export const IconPickerButton = ({
	value,
	icon,
	label,
	onOpen,
	onReset,
	openLabel,
	resetLabel
}) => (
	<div className="x3p0-breadcrumbs-icon-control">
		<div className="x3p0-breadcrumbs-icon-control__preview">
			<Button
				className="x3p0-breadcrumbs-icon-control__toggle"
				onClick={onOpen}
				aria-label={openLabel}
			>
				<span className="x3p0-breadcrumbs-icon-control__toggle-inner">
					<span className="x3p0-breadcrumbs-icon-control__icon">
						{value ? icon : (
							<Icon icon={notAllowed} className="x3p0-breadcrumbs-icon-control__empty-icon" />
						)}
					</span>
					<Truncate numberOfLines={1}>
						{label}
					</Truncate>
				</span>
			</Button>
			{onReset && (
				<Button
					className="x3p0-breadcrumbs-icon-control__reset"
					label={resetLabel}
					size="small"
					icon={resetIcon}
					onClick={onReset}
				/>
			)}
		</div>
	</div>
);
