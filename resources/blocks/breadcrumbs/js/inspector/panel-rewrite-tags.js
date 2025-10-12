/**
 * Rewrite tags panel.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

import { useInstanceId } from '@wordpress/compose';
import { useEntityRecords } from '@wordpress/core-data';
import { RawHTML } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import {
	__experimentalToolsPanel as ToolsPanel,
	__experimentalToolsPanelItem as ToolsPanelItem,
	ToggleControl
} from '@wordpress/components';

const RewriteTagsPanel = ({ attributes, setAttributes }) => {
	const panelId = useInstanceId(RewriteTagsPanel);
	const { mapRewriteTags = {} } = attributes;

	// Get viewable post types with rewrite tags.
	const { records: allPostTypes } = useEntityRecords('root', 'postType', {
		per_page: -1
	});

	const postTypes = allPostTypes?.filter(postType => {
		if (!postType.viewable) {
			return false;
		}

		const rewrite = postType['x3p0-breadcrumbs/rewrite'];
		return rewrite?.slug?.includes('%');
	}) || [];

	// Explicitly set `false` for the `post` post type. This is because it's
	// enabled by default, so we need to explicitly tell the Breadcrumbs
	// script on the PHP end not to map tags for it.
	const onRewriteTagChange = (postType, checked) => {
		const updatedRewriteTags = { ...mapRewriteTags };

		if (checked) {
			updatedRewriteTags[postType] = true;
		} else if ('post' === postType) {
			updatedRewriteTags[postType] = false;
		} else {
			delete updatedRewriteTags[postType];
		}

		setAttributes({ mapRewriteTags: updatedRewriteTags });
	};

	// Reset handler for ToolsPanelItem.
	const resetPanelItem = (postTypeSlug) => () =>
		onRewriteTagChange(postTypeSlug, false);

	// Resets the post rewrite tags to no mapping. Explicitly set the `post`
	// post type since it's `true` by default.
	const resetPanel = () => setAttributes({ mapRewriteTags: { post: false } });

	// Bail early if no post types with rewrite tags.
	if (postTypes.length === 0) {
		return null;
	}

	return (
		<ToolsPanel
			label={__('Rewrite Tags', 'x3p0-breadcrumbs')}
			resetAll={resetPanel}
			panelId={panelId}
		>
			<div style={{ gridColumn: '1 / -1' }}>
				<RawHTML>
					{sprintf(
						__('Map permalink rewrite tags, such as %1$s and %2$s, to breadcrumbs on single post views.', 'x3p0-breadcrumbs'),
						'<code>%category%</code>',
						'<code>%author%</code>'
					)}
				</RawHTML>
			</div>
			{postTypes.map((postType) => (
				<ToolsPanelItem
					key={postType.slug}
					hasValue={() => !!mapRewriteTags[postType.slug]}
					label={postType.labels.singular_name}
					onDeselect={resetPanelItem(postType.slug)}
					panelId={panelId}
				>
					<ToggleControl
						__nextHasNoMarginBottom
						label={postType.labels.singular_name}
						checked={mapRewriteTags[postType.slug] || false}
						onChange={() => onRewriteTagChange(
							postType.slug,
							! mapRewriteTags[postType.slug]
						)}
					/>
				</ToolsPanelItem>
			))}
		</ToolsPanel>
	);
};

export default RewriteTagsPanel;
