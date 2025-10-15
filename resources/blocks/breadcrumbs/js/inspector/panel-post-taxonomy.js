/**
 * Post taxonomies panel.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

import { useInstanceId } from '@wordpress/compose';
import { useEntityRecords } from '@wordpress/core-data';
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
	const { records: allPostTypes } = useEntityRecords('root', 'postType', {
		per_page: -1
	});

	const { records: allTaxonomies } = useEntityRecords('root', 'taxonomy', {
		per_page: -1
	});

	const postTypes  = allPostTypes?.filter(type => type.viewable) || [];
	const taxonomies = allTaxonomies?.filter(tax => tax.visibility?.publicly_queryable) || [];

	// Get taxonomies for a specific post type.
	const getPostTaxonomies = (postType) => taxonomies.filter(
		tax => tax.types?.includes(postType)
	);

	// Get taxonomy options for select control.
	const getTaxonomyOptions = (postType) => [
		{
			label: __('None', 'x3p0-breadcrumbs'),
			value: ''
		},
		...getPostTaxonomies(postType).map(tax => ({
			label: tax.labels.singular_name,
			value: tax.slug
		}))
	];

	// Updates the post taxonomy object when an option changes. We can
	// delete post types without assigned taxonomies from the attribute
	// since they are not enabled by default.
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
	const resetPanelItem = (postType) => () => {
		const { [postType]: _, ...updatedPostTaxonomy } = postTaxonomy;
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
				const postTypeTaxonomies = getPostTaxonomies(postType.slug);

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
