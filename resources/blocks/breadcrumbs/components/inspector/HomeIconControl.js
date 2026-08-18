/**
 * Home icon control component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Internal dependencies.
import { IconLibraryModal, IconPickerButton } from '../ui';

// WordPress dependencies.
import { __ } from '@wordpress/i18n';
import { home as controlIcon } from '@wordpress/icons';
import { store as coreStore } from '@wordpress/core-data';
import { safeHTML } from '@wordpress/dom';
import { useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';

/**
 * Renders the home icon control. The home icon only ever comes from the
 * registered icon library — there's no text/emoji option, as there is for
 * the separator — so the preview button opens the library directly rather
 * than through an intermediate dropdown.
 * @param props
 * @returns {JSX.Element}
 */
const HomeIconControl = ({
	attributes: {
		homeIcon
	},
	setAttributes
}) => {
	const [isLibraryOpen, setLibraryOpen] = useState(false);

	// Mirrors the selected icon on the preview button itself, falling back
	// to the generic home icon while nothing is selected (or its content is
	// still resolving).
	const selectedIcon = useSelect(
		(select) => homeIcon
			? select(coreStore).getEntityRecord('root', 'icon', homeIcon)
			: null,
		[homeIcon]
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
				value={homeIcon}
				icon={preview}
				label={__('Home', 'x3p0-breadcrumbs')}
				onOpen={() => setLibraryOpen(true)}
				onReset={() => setAttributes({ homeIcon: '' })}
				openLabel={__('Replace home icon', 'x3p0-breadcrumbs')}
				resetLabel={__('Remove home icon', 'x3p0-breadcrumbs')}
			/>
			{isLibraryOpen && (
				<IconLibraryModal
					value={homeIcon}
					title={__('Home Icon', 'x3p0-breadcrumbs')}
					description={__('Pick an icon for the home breadcrumb item.', 'x3p0-breadcrumbs')}
					onSelect={(value) => {
						setAttributes({ homeIcon: value });
						setLibraryOpen(false);
					}}
					onReset={() => {
						setAttributes({ homeIcon: '' });
						setLibraryOpen(false);
					}}
					onRequestClose={() => setLibraryOpen(false)}
				/>
			)}
		</>
	);
};

export default HomeIconControl;
