/**
 * Post taxonomies panel.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

import { useInstanceId } from '@wordpress/compose';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { __ } from '@wordpress/i18n';
import {
	SelectControl,
	__experimentalToolsPanel as ToolsPanel,
	__experimentalToolsPanelItem as ToolsPanelItem
} from '@wordpress/components';

const PostTaxonomyPanel = ({ attributes, setAttributes }) => {
	const panelId = useInstanceId(PostTaxonomyPanel);
	const { postTaxonomy = {} } = attributes;

	// Get viewable post types and taxonomies.
	const { postTypes, taxonomies } = useSelect((select) => {
		const { getPostTypes, getTaxonomies } = select(coreStore);

		const types = getPostTypes({ per_page: -1 }) || [];
		const taxes = getTaxonomies({ per_page: -1 }) || [];

		return {
			postTypes: types.filter(type => type.viewable),
			taxonomies: taxes.filter(tax => tax.visibility?.publicly_queryable)
		};
	}, []);

	// Get taxonomies for a specific post type.
	const getTaxonomiesForPostType = (postTypeSlug) =>
		taxonomies.filter(tax => tax.types?.includes(postTypeSlug));

	// Get taxonomy options for select control.
	const getTaxonomyOptions = (postTypeSlug) => [
		{ label: __('None', 'x3p0-breadcrumbs'), value: '' },
		...getTaxonomiesForPostType(postTypeSlug).map(tax => ({
			label: tax.labels.singular_name,
			value: tax.slug
		}))
	];

	// Updates the post taxonomy object when an option changes.
	const onTaxonomyChange = (postType, taxonomy) => {
		const updatedPostTaxonomy = { ...postTaxonomy };

		if (taxonomy) {
			updatedPostTaxonomy[postType] = taxonomy;
		} else {
			delete updatedPostTaxonomy[postType];
		}

		setAttributes({ postTaxonomy: updatedPostTaxonomy });
	};

	// Reset handler for ToolsPanelItem.
	const resetPanelItem = (postTypeSlug) => () => {
		const { [postTypeSlug]: _, ...updatedPostTaxonomy } = postTaxonomy;
		setAttributes({ postTaxonomy: updatedPostTaxonomy });
	};

	// Resets the post taxonomies to the default.
	const resetPanel = () => setAttributes({ postTaxonomy: {} });

	return (
		<ToolsPanel
			label={__('Post Taxonomies', 'x3p0-breadcrumbs')}
			resetAll={resetPanel}
			panelId={panelId}
		>
			<div style={{ gridColumn: '1 / -1' }}>
				{__('Select a taxonomy to appear in the breadcrumb trail for single post views.', 'x3p0-breadcrumbs')}
			</div>
			{postTypes.map((postType) => {
				const postTypeTaxonomies = getTaxonomiesForPostType(postType.slug);

				// Only show dropdown if post type has taxonomies.
				if (0 === postTypeTaxonomies.length) {
					return null;
				}

				return (
					<ToolsPanelItem
						key={postType.slug}
						hasValue={() => !! postTaxonomy[postType.slug]}
						label={postType.labels.singular_name}
						onDeselect={resetPanelItem(postType.slug)}
						panelId={panelId}
					>
						<SelectControl
							__next40pxDefaultSize
							__nextHasNoMarginBottom
							label={postType.labels.singular_name}
							value={postTaxonomy[postType.slug] || ''}
							options={getTaxonomyOptions(postType.slug)}
							onChange={(value) => onTaxonomyChange(postType.slug, value)}
						/>
					</ToolsPanelItem>
				);
			})}
		</ToolsPanel>
	);
};

export default PostTaxonomyPanel;
