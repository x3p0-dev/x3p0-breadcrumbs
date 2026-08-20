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
 * Renders the separator icon control for the `separator` slot of the `icons`
 * map. Like `IconControl`, it takes its value/change handler as props rather
 * than reading/writing `attributes`/`setAttributes` itself; unlike it, the
 * separator also offers built-in plain-text/glyph options that aren't
 * registrable SVG icons (see `SEPARATOR_ICONS`), so those are passed to the
 * library modal as `extraOptions` to surface alongside the registered icons
 * on the plugin's own collection tab. An empty value means the registered
 * default (passed via `defaultIcon`) renders, so the preview shows that
 * default rather than an empty state, and picking it stores nothing.
 * @param props
 * @returns {JSX.Element}
 */
const SeparatorIconControl = ({
	value,
	onChange,
	defaultIcon
}) => {
	const [isLibraryOpen, setLibraryOpen] = useState(false);

	// What actually renders on the front end: the user's choice, else the
	// registered default.
	const resolved = value || defaultIcon;

	// Mirrors the resolved icon on the preview button itself: a built-in
	// text/glyph option, or one fetched from the registered icon library,
	// falling back to a generic glyph while its content is still resolving.
	const builtIn = SEPARATOR_ICONS.find((option) => option.value === resolved);

	const selectedIcon = useSelect(
		(select) => (! builtIn && resolved?.includes('/'))
			? select(coreStore).getEntityRecord('root', 'icon', resolved)
			: null,
		[builtIn, resolved]
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

	// Picking the default stores nothing — the registered default already
	// renders it, so the attribute only ever holds real overrides.
	const pick = (next) => onChange(next === defaultIcon ? '' : next);

	return (
		<>
			<IconPickerButton
				value={resolved}
				icon={preview}
				label={__('Separator', 'x3p0-breadcrumbs')}
				onOpen={() => setLibraryOpen(true)}
				onReset={() => onChange('')}
				openLabel={__('Replace separator icon', 'x3p0-breadcrumbs')}
				resetLabel={__('Reset separator icon', 'x3p0-breadcrumbs')}
			/>
			{isLibraryOpen && (
				<IconLibraryModal
					value={resolved}
					title={__('Separator Icon', 'x3p0-breadcrumbs')}
					description={__('Pick an icon or symbol that sits in between and separates breadcrumb items.', 'x3p0-breadcrumbs')}
					extraOptions={SEPARATOR_ICONS}
					onSelect={(next) => {
						pick(next);
						setLibraryOpen(false);
					}}
					onReset={() => {
						onChange('');
						setLibraryOpen(false);
					}}
					resetValue={defaultIcon}
					onRequestClose={() => setLibraryOpen(false)}
				/>
			)}
		</>
	);
};

export default SeparatorIconControl;
