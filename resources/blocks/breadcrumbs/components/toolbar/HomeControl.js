/**
 * Home control component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Internal dependencies.
import { IconLibraryModal } from '../ui';

// WordPress dependencies.
import { __ } from '@wordpress/i18n';
import { home as controlIcon } from '@wordpress/icons';
import { store as coreStore } from '@wordpress/core-data';
import { safeHTML } from '@wordpress/dom';
import { useEffect, useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import { ToolbarButton } from '@wordpress/components';

/**
 * Renders the home icon and related controls. The home icon only ever comes
 * from the registered icon library — there's no text/emoji option, as there
 * is for the separator — so the toolbar button opens the library directly
 * rather than through an intermediate dropdown.
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
	const [isLibraryOpen, setLibraryOpen] = useState(false);

	useEffect(() => {
		if (! showHomeLabel && ! homeIcon) {
			setAttributes({ showHomeLabel: true });
		}
	}, [ homeIcon, showHomeLabel ]);

	// Mirrors the selected icon on the toolbar button itself, falling back
	// to the generic home icon while nothing is selected (or its content is
	// still resolving).
	const selectedIcon = useSelect(
		(select) => homeIcon
			? select(coreStore).getEntityRecord('root', 'icon', homeIcon)
			: null,
		[homeIcon]
	);

	const toolbarIcon = selectedIcon?.content ? (
		<span
			className="x3p0-breadcrumbs-toolbar-button__icon"
			dangerouslySetInnerHTML={{__html: safeHTML(selectedIcon.content)}}
		/>
	) : controlIcon;

	if (! showTrailStart) {
		return null;
	}

	return (
		<>
			<ToolbarButton
				icon={toolbarIcon}
				label={__('Home Icon', 'x3p0-breadcrumbs')}
				onClick={() => setLibraryOpen(true)}
				isPressed={!! homeIcon}
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

export default HomeControl;
