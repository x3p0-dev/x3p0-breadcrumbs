/**
 * Icons panel component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Internal dependencies.
import HomeIconControl      from './HomeIconControl';
import IconControl          from './IconControl';
import SeparatorIconControl from './SeparatorIconControl';

// WordPress dependencies.
import { getBlockType } from '@wordpress/blocks';
import { useInstanceId } from '@wordpress/compose';
import { useEntityRecords } from '@wordpress/core-data';
import { useMemo } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { archive as archiveIcon, post as postIcon, tag as tagIcon } from '@wordpress/icons';
import {
	__experimentalToolsPanel as ToolsPanel,
	__experimentalToolsPanelItem as ToolsPanelItem,
	CustomSelectControl
} from '@wordpress/components';

// Shared by every icon `ToolsPanelItem` below purely for CSS purposes: it
// lets adjacent items present as one flush block (see `_index.scss`) while
// remaining fully independent, separately-togglable panel items — the same
// way WordPress groups its own Background or Color panel rows.
const ITEM_CLASS_NAME = 'x3p0-breadcrumbs-icon-control-item';

// Icon/label visibility options are defined once in PHP via `IconVisibility`
// and `LabelVisibility` and passed in on the `x3p0Breadcrumbs` global, so the
// editor never recreates either list. Labels arrive pre-translated from the
// server.
//
// noinspection JSUnresolvedVariable
const ICON_VISIBILITY_OPTIONS = window.x3p0Breadcrumbs?.iconVisibilityOptions ?? [];

// noinspection JSUnresolvedVariable
const LABEL_VISIBILITY_OPTIONS = window.x3p0Breadcrumbs?.labelVisibilityOptions ?? [];

/**
 * Returns the first candidate label not already claimed within this panel,
 * claiming it in `usedLabels` as it goes. `ToolsPanelItem` tracks (and
 * displays, in its "+" menu) items by their `label` — not by a separate id —
 * so two post types/taxonomies sharing a label (e.g. core's `post_tag` and
 * WooCommerce's `product_tag`, both "Tag") would otherwise collide there.
 * Falls through singular name -> plural name -> a slug-qualified label
 * (always unique), so the common case never shows the slug at all — only
 * an actual collision does.
 */
const uniqueLabel = (usedLabels, ...candidates) => {
	const label = candidates.find((candidate) => candidate && ! usedLabels.has(candidate))
		?? candidates[candidates.length - 1];

	usedLabels.add(label);

	return label;
};

/**
 * Renders a `<ToolsPanel>` component with the block's icon controls: the
 * home and separator icons, one row per viewable post type's single-post
 * icon (and a second for its archive icon, when it has one), and one row per
 * public taxonomy's term icon. Post types and taxonomies are read live via
 * `core-data`, so a new one gets a row automatically — no JS change needed.
 * Unlike the Post/Page post type rows, no taxonomy row is shown by default;
 * every one starts hidden behind the panel's "+" menu. More icon settings
 * will join as additional panel items as they're added.
 * @param props
 * @returns {JSX.Element}
 */
const IconsPanel = (props) => {
	const {
		attributes: { homeIcon, separatorIcon, showTrailStart, postTypeIcons = {}, taxonomyIcons = {}, iconVisibility, labelVisibility },
		setAttributes
	} = props;

	const panelId = useInstanceId(IconsPanel);

	// Prefer the (possibly filtered) PHP-supplied default, which should be
	// set for the block metadata; fall back to a literal as a last resort.
	const defaultSeparatorIcon = useMemo(
		() => getBlockType('x3p0/breadcrumbs')?.attributes?.separatorIcon?.default
			?? 'x3p0-breadcrumbs/chevron',
		[]
	);

	const defaultIconVisibility = useMemo(
		() => getBlockType('x3p0/breadcrumbs')?.attributes?.iconVisibility?.default ?? 'none',
		[]
	);

	const defaultLabelVisibility = useMemo(
		() => getBlockType('x3p0/breadcrumbs')?.attributes?.labelVisibility?.default ?? 'all',
		[]
	);

	// Every crumb's icon (home, post type single/archive) is hidden while
	// icon visibility is "none" — only the separator is unaffected, since
	// it isn't a crumb icon.
	const iconsHidden = 'none' === iconVisibility;

	const { records: allPostTypes } = useEntityRecords('root', 'postType', {
		per_page: -1
	});

	const postTypes = allPostTypes?.filter(type => type.viewable) || [];

	const { records: allTaxonomies } = useEntityRecords('root', 'taxonomy', {
		per_page: -1
	});

	const taxonomies = allTaxonomies?.filter(tax => tax.visibility?.publicly_queryable) || [];

	// Updates a single icon slot ('single' or 'archive') for a post type,
	// dropping empty slots and empty post type entries from the attribute so
	// it only ever stores real overrides.
	const onPostTypeIconChange = (postType, slot, value) => {
		const updatedPostTypeIcons = {...postTypeIcons};
		const entry = {...(updatedPostTypeIcons[postType] || {})};

		if (value) {
			entry[slot] = value;
		} else {
			delete entry[slot];
		}

		if (Object.keys(entry).length) {
			updatedPostTypeIcons[postType] = entry;
		} else {
			delete updatedPostTypeIcons[postType];
		}

		setAttributes({ postTypeIcons: updatedPostTypeIcons });
	};

	// Updates a taxonomy's icon, dropping empty entries from the attribute
	// so it only ever stores real overrides.
	const onTaxonomyIconChange = (taxonomy, value) => {
		const updatedTaxonomyIcons = {...taxonomyIcons};

		if (value) {
			updatedTaxonomyIcons[taxonomy] = value;
		} else {
			delete updatedTaxonomyIcons[taxonomy];
		}

		setAttributes({ taxonomyIcons: updatedTaxonomyIcons });
	};

	// Shared across the post type and taxonomy rows below (in render order)
	// so a label is only ever disambiguated against what's already claimed.
	const usedLabels = new Set();

	return (
		<ToolsPanel
			label={__('Icons', 'x3p0-breadcrumbs')}
			resetAll={() => setAttributes({
				homeIcon: '',
				separatorIcon: defaultSeparatorIcon,
				postTypeIcons: undefined,
				taxonomyIcons: undefined,
				iconVisibility: defaultIconVisibility,
				labelVisibility: defaultLabelVisibility
			})}
			panelId={panelId}
		>
			<ToolsPanelItem
				label={__('Icon Visibility', 'x3p0-breadcrumbs')}
				hasValue={() => iconVisibility !== defaultIconVisibility}
				onDeselect={() => setAttributes({ iconVisibility: defaultIconVisibility })}
				panelId={panelId}
				isShownByDefault
			>
				<CustomSelectControl
					label={__('Icon Visibility', 'x3p0-breadcrumbs')}
					options={ICON_VISIBILITY_OPTIONS}
					value={ICON_VISIBILITY_OPTIONS.find(
						(option) => option.key === iconVisibility
					)}
					onChange={({ selectedItem }) => setAttributes({
						iconVisibility: selectedItem.key
					})}
				/>
			</ToolsPanelItem>
			{! iconsHidden && (
				<ToolsPanelItem
					label={__('Label Visibility', 'x3p0-breadcrumbs')}
					hasValue={() => labelVisibility !== defaultLabelVisibility}
					onDeselect={() => setAttributes({ labelVisibility: defaultLabelVisibility })}
					panelId={panelId}
					isShownByDefault
				>
					<CustomSelectControl
						label={__('Label Visibility', 'x3p0-breadcrumbs')}
						options={LABEL_VISIBILITY_OPTIONS}
						value={LABEL_VISIBILITY_OPTIONS.find(
							(option) => option.key === labelVisibility
						)}
						onChange={({ selectedItem }) => setAttributes({
							labelVisibility: selectedItem.key
						})}
					/>
				</ToolsPanelItem>
			)}
			<ToolsPanelItem
				className={ITEM_CLASS_NAME}
				label={__('Separator', 'x3p0-breadcrumbs')}
				hasValue={() => separatorIcon !== defaultSeparatorIcon}
				onDeselect={() => setAttributes({ separatorIcon: defaultSeparatorIcon })}
				panelId={panelId}
				isShownByDefault
			>
				<SeparatorIconControl {...props} defaultSeparatorIcon={defaultSeparatorIcon} />
			</ToolsPanelItem>
			{! iconsHidden && showTrailStart && (
				<ToolsPanelItem
					className={ITEM_CLASS_NAME}
					label={__('Home', 'x3p0-breadcrumbs')}
					hasValue={() => !! homeIcon}
					onDeselect={() => setAttributes({ homeIcon: '' })}
					panelId={panelId}
					isShownByDefault
				>
					<HomeIconControl {...props} />
				</ToolsPanelItem>
			)}
			{! iconsHidden && postTypes.flatMap((postType) => {
				const entry = postTypeIcons[postType.slug] || {};

				const rows = [{
					slot: 'single',
					label: uniqueLabel(
						usedLabels,
						postType.labels.singular_name,
						postType.labels.template_name,
						sprintf('%1$s (%2$s)', postType.labels.singular_name, postType.slug)
					),
					controlIcon: postIcon,
					description: __('Pick an icon for this post type’s single post breadcrumb item.', 'x3p0-breadcrumbs'),
					isShownByDefault: 'post' === postType.slug || 'page' === postType.slug
				}];

				if (postType.has_archive) {
					rows.push({
						slot: 'archive',
						label: uniqueLabel(
							usedLabels,
							postType.labels.archives,
							postType.labels.name,
							sprintf('%1$s (%2$s)', postType.labels.archives, postType.slug)
						),
						controlIcon: archiveIcon,
						description: __('Pick an icon for this post type’s archive breadcrumb item.', 'x3p0-breadcrumbs'),
						isShownByDefault: false
					});
				}

				return rows.map((row) => (
					<ToolsPanelItem
						key={`${postType.slug}-${row.slot}`}
						className={ITEM_CLASS_NAME}
						label={row.label}
						hasValue={() => !! entry[row.slot]}
						onDeselect={() => onPostTypeIconChange(postType.slug, row.slot, '')}
						panelId={panelId}
						isShownByDefault={row.isShownByDefault}
					>
						<IconControl
							value={entry[row.slot] || ''}
							onChange={(value) => onPostTypeIconChange(postType.slug, row.slot, value)}
							label={row.label}
							controlIcon={row.controlIcon}
							openLabel={sprintf(__('Replace %s icon', 'x3p0-breadcrumbs'), row.label)}
							resetLabel={sprintf(__('Remove %s icon', 'x3p0-breadcrumbs'), row.label)}
							modalTitle={sprintf(__('%s Icon', 'x3p0-breadcrumbs'), row.label)}
							modalDescription={row.description}
						/>
					</ToolsPanelItem>
				));
			})}
			{! iconsHidden && taxonomies.map((taxonomy) => {
				const label = uniqueLabel(
					usedLabels,
					taxonomy.labels.singular_name,
					taxonomy.labels.name,
					taxonomy.labels.template_name,
					sprintf('%1$s (%2$s)', taxonomy.labels.singular_name, taxonomy.slug)
				);

				return (
					<ToolsPanelItem
						key={taxonomy.slug}
						className={ITEM_CLASS_NAME}
						label={label}
						hasValue={() => !! taxonomyIcons[taxonomy.slug]}
						onDeselect={() => onTaxonomyIconChange(taxonomy.slug, '')}
						panelId={panelId}
					>
						<IconControl
							value={taxonomyIcons[taxonomy.slug] || ''}
							onChange={(value) => onTaxonomyIconChange(taxonomy.slug, value)}
							label={label}
							controlIcon={tagIcon}
							openLabel={sprintf(__('Replace %s icon', 'x3p0-breadcrumbs'), label)}
							resetLabel={sprintf(__('Remove %s icon', 'x3p0-breadcrumbs'), label)}
							modalTitle={sprintf(__('%s Icon', 'x3p0-breadcrumbs'), label)}
							modalDescription={__('Pick an icon for this taxonomy’s term breadcrumb item.', 'x3p0-breadcrumbs')}
						/>
					</ToolsPanelItem>
				);
			})}
		</ToolsPanel>
	);
};

export default IconsPanel;
