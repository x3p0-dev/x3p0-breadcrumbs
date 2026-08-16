/**
 * Icon library modal component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Internal dependencies.
import { IconGrid } from './IconGrid';

// WordPress dependencies.
import { __ } from '@wordpress/i18n';
import { Button, Icon, Modal, SearchControl, Spinner, TabPanel } from '@wordpress/components';
import { chevronRight } from '@wordpress/icons';
import { store as coreStore } from '@wordpress/core-data';
import { useDebounce } from '@wordpress/compose';
import { useMemo, useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';

/**
 * Renders a full-screen modal for browsing and selecting icons registered
 * through the WordPress 7.1 icon registration API. Collections become
 * tabs; icons within the active tab (or all of them, under "All") are
 * filtered by an optional search term.
 *
 * `value` and the argument passed to `onSelect` both use the REST API's
 * `{collection}/{name}` icon identifier, matching core's own icon picker
 * so this component can later be swapped for an eventual public
 * `IconPickerModal` from `@wordpress/block-editor` with no contract change.
 *
 * `onReset`, when provided, renders a "Reset Icon" button alongside the
 * search field for resetting the icon back to `resetValue` — the grid
 * itself has no dedicated tile for it, since every tile is a real
 * registered icon (or one of `extraOptions`).
 *
 * `extraOptions` — `{value, label, icon}` entries, matching the shape of
 * `SEPARATOR_ICONS` in `utils/constants.js` — are appended to
 * the grid only while `defaultCollection`'s tab is active. This is how
 * non-registrable choices (e.g. the separator's plain-text/glyph options)
 * surface alongside the plugin's own registered icons.
 * @param props
 * @returns {JSX.Element}
 */
export const IconLibraryModal = ({
	value,
	title,
	description,
	defaultCollection = 'x3p0-breadcrumbs',
	extraOptions,
	onSelect,
	onReset,
	resetValue = '',
	onRequestClose
}) => {
	const [inputValue, setInputValue] = useState('');
	const [search, setSearch] = useState('');
	const debouncedSetSearch = useDebounce(setSearch, 300);

	const handleSearchChange = (next) => {
		setInputValue(next);
		debouncedSetSearch(next);
	};

	const collections = useSelect(
		(select) => select(coreStore).getEntityRecords('root', 'iconCollection'),
		[]
	);

	// `TabPanel` is the only tab component in the public API; the compound
	// `Tabs` component core's own icon picker uses (and which renders this
	// hover/active chevron itself) is locked behind `@wordpress/private-apis`
	// and unavailable to third-party code, so the chevron is added to each
	// tab's `title` here instead, with CSS driving its hover/active opacity.
	const tabs = useMemo(() => [
		{name: '', title: __('All', 'x3p0-breadcrumbs')},
		...(collections ?? []).map((collection) => ({
			name: collection.slug,
			title: collection.label
		}))
	].map((tab) => ({
		...tab,
		title: (
			<>
				{tab.title}
				<Icon icon={chevronRight} className="x3p0-breadcrumbs-icon-library-modal__tab-chevron" />
			</>
		)
	})), [collections]);

	const initialTabName = value?.includes('/') ? value.split('/')[0] : defaultCollection;

	return (
		<Modal
			className="x3p0-breadcrumbs-icon-library-modal"
			title={title ?? __('Icon Library', 'x3p0-breadcrumbs')}
			onRequestClose={onRequestClose}
			isFullScreen
		>
			<div className="x3p0-breadcrumbs-icon-library-modal__inserter">
				{description && (
					<p className="x3p0-breadcrumbs-icon-library-modal__description">
						{description}
					</p>
				)}
				<div className="x3p0-breadcrumbs-icon-library-modal__header">
					<SearchControl
						className="x3p0-breadcrumbs-icon-library-modal__search"
						label={__('Search icons', 'x3p0-breadcrumbs')}
						hideLabelFromVision
						value={inputValue}
						onChange={handleSearchChange}
					/>
					{onReset && (
						<Button
							variant="secondary"
							onClick={onReset}
							disabled={value === resetValue}
							__next40pxDefaultSize
						>
							{__('Reset Icon', 'x3p0-breadcrumbs')}
						</Button>
					)}
				</div>

				<TabPanel
					className="x3p0-breadcrumbs-icon-library-modal__tabs"
					orientation="vertical"
					tabs={tabs}
					initialTabName={initialTabName}
				>
					{(tab) => (
						<IconLibraryPanel
							collection={tab.name}
							search={search}
							value={value}
							onSelect={onSelect}
							extraOptions={tab.name === defaultCollection ? extraOptions : undefined}
						/>
					)}
				</TabPanel>
			</div>
		</Modal>
	);
};

/**
 * Resolves and renders the icons belonging to a single collection tab (or
 * every collection, when `collection` is empty, i.e. the "All" tab), plus
 * any `extraOptions` — non-registered choices such as the separator's
 * plain-text/glyph options — filtered by the same search term and appended
 * to the grid.
 * @param props
 * @returns {JSX.Element}
 */
const IconLibraryPanel = ({
	collection,
	search,
	value,
	onSelect,
	extraOptions = []
}) => {
	const query = useMemo(() => {
		const args = {};

		if (collection) {
			args.collection = collection;
		}

		if (search) {
			args.search = search;
		}

		return args;
	}, [collection, search]);

	const {icons, hasResolved} = useSelect((select) => {
		const {getEntityRecords, hasFinishedResolution} = select(coreStore);

		return {
			icons: getEntityRecords('root', 'icon', query),
			hasResolved: hasFinishedResolution('getEntityRecords', ['root', 'icon', query])
		};
	}, [query]);

	const filteredExtras = useMemo(() => {
		const term = search.trim().toLowerCase();

		return extraOptions
			.filter((option) => ! term || option.label.toLowerCase().includes(term))
			.map((option) => ({
				name: option.value,
				label: option.label,
				text: option.icon
			}));
	}, [extraOptions, search]);

	if (! hasResolved) {
		return (
			<div className="x3p0-breadcrumbs-icon-library-modal__loading">
				<Spinner />
			</div>
		);
	}

	return (
		<IconGrid icons={[...(icons ?? []), ...filteredExtras]} value={value} onSelect={onSelect} />
	);
};
