/**
 * Separator control component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Internal dependencies.
import { IconLibraryModal } from '../ui';
import { SEPARATOR_ICONS } from '../../utils/constants';

// WordPress dependencies.
import { __ } from '@wordpress/i18n';
import { next as controlIcon } from '@wordpress/icons';
import { getBlockType } from '@wordpress/blocks';
import { store as coreStore } from '@wordpress/core-data';
import { safeHTML } from '@wordpress/dom';
import { useMemo, useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import { ToolbarButton } from '@wordpress/components';

/**
 * Renders the separator icon control. Unlike the home icon, the separator
 * also offers built-in plain-text/glyph options that aren't registrable SVG
 * icons (see `SEPARATOR_ICONS`), so those are passed to the library modal
 * as `extraOptions` to surface alongside the registered icons on the
 * plugin's own collection tab.
 * @param props
 * @returns {JSX.Element}
 */
const SeparatorControl = ({ attributes: { separatorIcon }, setAttributes }) => {
	const [isLibraryOpen, setLibraryOpen] = useState(false);

	// Prefer the (possibly filtered) PHP-supplied default, which should be
	// set for the block metadata; fall back to a literal as a last resort.
	const defaultSeparatorIcon = useMemo(
		() => getBlockType('x3p0/breadcrumbs')?.attributes?.separatorIcon?.default
			?? 'x3p0-breadcrumbs/chevron',
		[]
	);

	// Mirrors the selected icon on the toolbar button itself: a built-in
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

	const toolbarIcon = builtIn ? (
		<span className="x3p0-breadcrumbs-toolbar-button__icon x3p0-breadcrumbs-toolbar-button__icon--text">
			{builtIn.icon}
		</span>
	) : selectedIcon?.content ? (
		<span
			className="x3p0-breadcrumbs-toolbar-button__icon"
			dangerouslySetInnerHTML={{__html: safeHTML(selectedIcon.content)}}
		/>
	) : controlIcon;

	return (
		<>
			<ToolbarButton
				icon={toolbarIcon}
				label={__('Separator Icon', 'x3p0-breadcrumbs')}
				onClick={() => setLibraryOpen(true)}
				isPressed={!! separatorIcon}
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

export default SeparatorControl;
