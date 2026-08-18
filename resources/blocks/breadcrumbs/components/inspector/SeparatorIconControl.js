/**
 * Separator icon control component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Internal dependencies.
import { IconLibraryModal, IconPickerButton } from '../ui';
import { SEPARATOR_ICONS } from '../../utils/constants';

// WordPress dependencies.
import { __ } from '@wordpress/i18n';
import { next as controlIcon } from '@wordpress/icons';
import { store as coreStore } from '@wordpress/core-data';
import { safeHTML } from '@wordpress/dom';
import { useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';

/**
 * Renders the separator icon control. Unlike the home icon, the separator
 * also offers built-in plain-text/glyph options that aren't registrable SVG
 * icons (see `SEPARATOR_ICONS`), so those are passed to the library modal
 * as `extraOptions` to surface alongside the registered icons on the
 * plugin's own collection tab.
 * @param props
 * @returns {JSX.Element}
 */
const SeparatorIconControl = ({
	attributes: { separatorIcon },
	setAttributes,
	defaultSeparatorIcon
}) => {
	const [isLibraryOpen, setLibraryOpen] = useState(false);

	// Mirrors the selected icon on the preview button itself: a built-in
	// text/glyph option, one fetched from the registered icon library, or
	// (while nothing is selected, or its content is still resolving) the
	// generic separator icon.
	const builtIn = SEPARATOR_ICONS.find((option) => option.value === separatorIcon);

	const selectedIcon = useSelect(
		(select) => (! builtIn && separatorIcon?.includes('/'))
			? select(coreStore).getEntityRecord('root', 'icon', separatorIcon)
			: null,
		[builtIn, separatorIcon]
	);

	const preview = builtIn ? (
		<span className="x3p0-breadcrumbs-icon-control__icon-text">
			{builtIn.icon}
		</span>
	) : selectedIcon?.content ? (
		<span
			className="x3p0-breadcrumbs-icon-control__icon-svg"
			dangerouslySetInnerHTML={{__html: safeHTML(selectedIcon.content)}}
		/>
	) : controlIcon;

	return (
		<>
			<IconPickerButton
				value={separatorIcon}
				icon={preview}
				label={__('Separator', 'x3p0-breadcrumbs')}
				onOpen={() => setLibraryOpen(true)}
				onReset={() => setAttributes({ separatorIcon: defaultSeparatorIcon })}
				openLabel={__('Replace separator icon', 'x3p0-breadcrumbs')}
				resetLabel={__('Reset separator icon', 'x3p0-breadcrumbs')}
			/>
			{isLibraryOpen && (
				<IconLibraryModal
					value={separatorIcon}
					title={__('Separator Icon', 'x3p0-breadcrumbs')}
					description={__('Pick an icon or symbol that sits in between and separates breadcrumb items.', 'x3p0-breadcrumbs')}
					extraOptions={SEPARATOR_ICONS}
					onSelect={(value) => {
						setAttributes({ separatorIcon: value });
						setLibraryOpen(false);
					}}
					onReset={() => {
						setAttributes({ separatorIcon: defaultSeparatorIcon });
						setLibraryOpen(false);
					}}
					resetValue={defaultSeparatorIcon}
					onRequestClose={() => setLibraryOpen(false)}
				/>
			)}
		</>
	);
};

export default SeparatorIconControl;
