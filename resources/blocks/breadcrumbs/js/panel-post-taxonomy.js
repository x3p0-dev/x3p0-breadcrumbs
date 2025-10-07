/**
 * Post taxonomies panel.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// WordPress dependencies.
import { useInstanceId } from '@wordpress/compose';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { __ } from '@wordpress/i18n';

import {
	SelectControl,
	__experimentalToolsPanel as ToolsPanel,
	__experimentalToolsPanelItem as ToolsPanelItem
} from '@wordpress/components';

// Exports the post taxonomy panel.
const PostTaxonomyPanel = ({
	attributes,
	setAttributes
}) => {
	const panelId = useInstanceId(PostTaxonomyPanel);

	const { postTaxonomy } = attributes;

	// Get viewable post types.
	const postTypes = useSelect((select) => {
		const types = select(coreStore).getPostTypes({ per_page: -1 });

		if (! types) {
			return [];
		}

		return Object.values(types).filter(
			postType => true === postType.viewable
		);
	}, []);

	// Get viewable taxonomies.
	const taxonomies = useSelect((select) => {
		const taxes = select(coreStore).getTaxonomies({ per_page: -1 });

		if (! taxes) {
			return [];
		}

		return Object.values(taxes).filter(
			taxonomy => true === taxonomy.visibility?.publicly_queryable
		);
	}, []);

	// Get taxonomies for a specific post type.
	const getTaxonomiesForPostType = (postTypeSlug) => taxonomies.filter(
		taxonomy => taxonomy.types && taxonomy.types.includes(postTypeSlug)
	);

	// Get taxonomy options for select control.
	const getTaxonomyOptions = (postTypeSlug) => {
		const postTypeTaxonomies = getTaxonomiesForPostType(postTypeSlug);

		return [
			{
				label: __('None', 'x3p0-breadcrumbs'),
				value: ''
			},
			...postTypeTaxonomies.map(taxonomy => ({
				label: taxonomy.labels.singular_name,
				value: taxonomy.slug
			}))
		];
	};

	// Updates the post taxonomy object when an option changes.
	const onTaxonomyChange = (postType, taxonomy) => {
		const updatedPostTaxonomy = {
			...postTaxonomy,
			[postType]: taxonomy
		};

		// Remove empty values
		if (! taxonomy) {
			delete updatedPostTaxonomy[postType];
		}

		setAttributes({ postTaxonomy: updatedPostTaxonomy });
	};

	// Reset handler for ToolsPanelItem
	const resetPanelItem = (postTypeSlug) => () => {
		const updatedPostTaxonomy = { ...postTaxonomy };
		delete updatedPostTaxonomy[postTypeSlug];
		setAttributes({ postTaxonomy: updatedPostTaxonomy });
	};

	// Resets the post taxonomies to the default.
	const resetPanel = () => setAttributes({
		postTaxonomy: {}
	});

	const postTaxonomyPanelItems = postTypes.map((postType) => {
		const postTypeTaxonomies = getTaxonomiesForPostType(postType.slug);

		// Only show dropdown if post type has taxonomies
		if (0 === postTypeTaxonomies.length) {
			return null;
		}

		const hasValue = !! postTaxonomy[postType.slug];

		return (
			<ToolsPanelItem
				key={postType.slug}
				hasValue={() => hasValue}
				label={postType.labels.singular_name}
				onDeselect={resetPanelItem(postType.slug)}
				panelId={ panelId }
			>
				<SelectControl
					label={postType.labels.singular_name}
					value={postTaxonomy[postType.slug] || ''}
					options={getTaxonomyOptions(postType.slug)}
					onChange={(value) => onTaxonomyChange(
						postType.slug,
						value
					)}
					__next40pxDefaultSize={true}
					__nextHasNoMarginBottom={true}
				/>
			</ToolsPanelItem>
		);
	}).filter(Boolean);

	return (
		<ToolsPanel
			label={__('Post Taxonomies', 'x3p0-breadcrumbs')}
			resetAll={ resetPanel }
			panelId={ panelId }
		>
			<div style={{ gridColumn: '1 / -1' }}>
				{__('Select a taxonomy to appear in the breadcrumb trail for single post views.', 'x3p0-breadcrumbs')}
			</div>
			{ postTaxonomyPanelItems }
		</ToolsPanel>
	);
};

export default PostTaxonomyPanel;
