/**
 * Icon option picker component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Internal dependencies.
import { OPTION_GROUPS } from '../../utils/icon-options';

// WordPress dependencies.
import { __, sprintf } from '@wordpress/i18n';
import { plus } from '@wordpress/icons';
import { useMemo, useState } from '@wordpress/element';
import {
	Button,
	Dropdown,
	MenuGroup,
	MenuItem,
	SearchControl
} from '@wordpress/components';

/**
 * Renders the "Add an icon" button that opens a searchable, grouped menu of
 * the icon options the panel isn't already showing a row for.
 *
 * This is what keeps the panel's size a function of what the user chose
 * rather than of how many options are registered: PHP registers one option
 * per viewable post type and public taxonomy, so the list is unbounded by
 * design, and a fixed set of rows (or a flat `ToolsPanel` "+" menu holding
 * every one of them) doesn't survive a site with a handful of custom post
 * types on it. Selecting an option hands its key back and closes the menu;
 * the panel adds the row.
 * @param props
 * @returns {JSX.Element}
 */
const IconOptionPicker = ({ options, onSelect }) => {
	const [search, setSearch] = useState('');

	// The offered options bucketed into the groups PHP registered them under,
	// filtered by the search term and with empty groups dropped, so the menu
	// only ever lists headings that have something under them. The name, slug,
	// and key are all searched: someone hunting for a specific post type is as
	// likely to type its slug as its label.
	const groups = useMemo(() => {
		const term = search.trim().toLowerCase();

		const matches = options.filter((option) => ! term
			|| option.name.toLowerCase().includes(term)
			|| option.slug.toLowerCase().includes(term)
			|| option.key.toLowerCase().includes(term));

		return OPTION_GROUPS
			.map((group) => ({
				...group,
				options: matches.filter((option) => group.key === option.group)
			}))
			.filter((group) => group.options.length > 0);
	}, [options, search]);

	return (
		<Dropdown
			className="x3p0-breadcrumbs-icons-panel__add"
			contentClassName="x3p0-breadcrumbs-icon-option-picker"
			popoverProps={{placement: 'bottom-start'}}
			// The search term is per-session, not per-opening: reset it on
			// close so reopening the menu always starts from the full list.
			onClose={() => setSearch('')}
			renderToggle={({isOpen, onToggle}) => (
				<Button
					className="x3p0-breadcrumbs-icons-panel__add-toggle"
					variant="secondary"
					icon={plus}
					onClick={onToggle}
					aria-expanded={isOpen}
					__next40pxDefaultSize
				>
					{__('Add an icon', 'x3p0-breadcrumbs')}
				</Button>
			)}
			renderContent={({onClose}) => (
				<>
					<SearchControl
						className="x3p0-breadcrumbs-icon-option-picker__search"
						label={__('Search icon settings', 'x3p0-breadcrumbs')}
						placeholder={__('Search', 'x3p0-breadcrumbs')}
						hideLabelFromVision
						value={search}
						onChange={setSearch}
					/>
					<div className="x3p0-breadcrumbs-icon-option-picker__menu">
						{groups.map((group) => (
							<MenuGroup key={group.key} label={group.name}>
								{group.options.map((option) => (
									<MenuItem
										key={option.key}
										// Two WordPress objects can share a
										// label, so the slug rides along to
										// tell them apart. An option with a
										// flat key of its own has none, and
										// needs none.
										info={option.slug || undefined}
										onClick={() => {
											onSelect(option.key);
											onClose();
										}}
									>
										{option.name}
									</MenuItem>
								))}
							</MenuGroup>
						))}
						{0 === groups.length && (
							<p className="x3p0-breadcrumbs-icon-option-picker__no-results">
								{sprintf(
									// translators: %s: search term.
									__('No icon settings found for "%s".', 'x3p0-breadcrumbs'),
									search.trim()
								)}
							</p>
						)}
					</div>
				</>
			)}
		/>
	);
};

export default IconOptionPicker;
