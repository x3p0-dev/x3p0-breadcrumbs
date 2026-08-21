/**
 * Icon control component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Internal dependencies.
import { IconLibraryModal, IconPickerButton } from '../ui';

// WordPress dependencies.
import { store as coreStore } from '@wordpress/core-data';
import { safeHTML } from '@wordpress/dom';
import { useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';

/**
 * Renders a single icon control for a value that doesn't live on its own
 * top-level block attribute — the home crumb's icon, a post type's
 * single-post/archive icon, or a taxonomy's term icon — but inside a map
 * keyed by option key. Like `SeparatorIconControl`, it takes its value and
 * change handler as props rather than reading and writing
 * `attributes`/`setAttributes` itself; the panel that renders it owns that
 * mapping.
 *
 * An empty value means the option's registered default renders, so the
 * preview shows that default (passed via `defaultIcon`) rather than an empty
 * state, and picking it back stores nothing. Showing what actually renders
 * keeps the row honest — the same reason crumbs resolve a single option key
 * instead of cascading through several.
 *
 * What the row's "-" button offers depends on whether the row can be taken
 * out of the panel. A removable row (`onRemove`) always offers that, whether
 * or not it currently holds an override — removing takes any override with
 * it, and the row is on screen because the user asked for it, so putting it
 * back away is the thing they are most likely to want. A row that cannot be
 * removed, such as a pinned one, passes no `onRemove` and offers a reset
 * instead, and only while there is an override to undo.
 *
 * Resetting to the default is therefore the modal's job for a removable row,
 * via its own "Reset Icon" button — which stays put and disables itself at
 * the default, so a modal opened on a default row isn't missing a control it
 * has everywhere else.
 * @param props
 * @returns {JSX.Element}
 */
const IconControl = ({
	value,
	onChange,
	onReset,
	onRemove,
	defaultIcon,
	label,
	controlIcon,
	openLabel,
	resetLabel,
	removeLabel,
	modalTitle,
	modalDescription
}) => {
	const [isLibraryOpen, setLibraryOpen] = useState(false);

	// What actually renders on the front end: the user's choice, else the
	// option's registered default.
	const resolved = value || defaultIcon;

	// Mirrors the resolved icon on the preview button itself, falling back
	// to the given generic icon while its content is still resolving.
	const selectedIcon = useSelect(
		(select) => resolved
			? select(coreStore).getEntityRecord('root', 'icon', resolved)
			: null,
		[resolved]
	);

	const preview = selectedIcon?.content ? (
		<span
			className="x3p0-breadcrumbs-icon-control__icon-svg"
			dangerouslySetInnerHTML={{__html: safeHTML(selectedIcon.content)}}
		/>
	) : controlIcon;

	// Picking the default stores nothing — the registered default already
	// renders it, so the attribute only ever holds real overrides.
	const pick = (next) => onChange(next === defaultIcon ? '' : next);

	const reset = onReset ?? (() => onChange(''));

	// What the row's "-" button is for: taking the row away where that is on
	// offer, and otherwise undoing an override there is one to undo.
	const rowAction = onRemove
		? {onClick: onRemove, label: removeLabel}
		: value ? {onClick: reset, label: resetLabel} : null;

	return (
		<>
			<IconPickerButton
				value={resolved}
				icon={preview}
				label={label}
				onOpen={() => setLibraryOpen(true)}
				onReset={rowAction?.onClick}
				openLabel={openLabel}
				resetLabel={rowAction?.label}
			/>
			{isLibraryOpen && (
				<IconLibraryModal
					value={resolved}
					title={modalTitle}
					description={modalDescription}
					onSelect={(next) => {
						pick(next);
						setLibraryOpen(false);
					}}
					onReset={() => {
						reset();
						setLibraryOpen(false);
					}}
					resetValue={defaultIcon}
					onRequestClose={() => setLibraryOpen(false)}
				/>
			)}
		</>
	);
};

export default IconControl;
