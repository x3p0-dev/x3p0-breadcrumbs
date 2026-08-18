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
 * top-level block attribute — a post type's single-post/archive icon, or a
 * taxonomy's term icon — but inside a map keyed by slug. Unlike
 * `HomeIconControl`/`SeparatorIconControl`, it takes its value/change handler
 * as props rather than reading/writing `attributes`/`setAttributes` itself;
 * the panel that renders it owns that mapping.
 * @param props
 * @returns {JSX.Element}
 */
const IconControl = ({
	value,
	onChange,
	label,
	controlIcon,
	openLabel,
	resetLabel,
	modalTitle,
	modalDescription
}) => {
	const [isLibraryOpen, setLibraryOpen] = useState(false);

	// Mirrors the selected icon on the preview button itself, falling back
	// to the given generic icon while nothing is selected (or its content is
	// still resolving).
	const selectedIcon = useSelect(
		(select) => value
			? select(coreStore).getEntityRecord('root', 'icon', value)
			: null,
		[value]
	);

	const preview = selectedIcon?.content ? (
		<span
			className="x3p0-breadcrumbs-icon-control__icon-svg"
			dangerouslySetInnerHTML={{__html: safeHTML(selectedIcon.content)}}
		/>
	) : controlIcon;

	return (
		<>
			<IconPickerButton
				value={value}
				icon={preview}
				label={label}
				onOpen={() => setLibraryOpen(true)}
				onReset={() => onChange('')}
				openLabel={openLabel}
				resetLabel={resetLabel}
			/>
			{isLibraryOpen && (
				<IconLibraryModal
					value={value}
					title={modalTitle}
					description={modalDescription}
					onSelect={(next) => {
						onChange(next);
						setLibraryOpen(false);
					}}
					onReset={() => {
						onChange('');
						setLibraryOpen(false);
					}}
					onRequestClose={() => setLibraryOpen(false)}
				/>
			)}
		</>
	);
};

export default IconControl;
