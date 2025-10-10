/**
 * Rewrite tags panel.
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
import { RawHTML } from "@wordpress/element";
import { __, sprintf } from '@wordpress/i18n';

import {
	__experimentalToolsPanel as ToolsPanel,
	__experimentalToolsPanelItem as ToolsPanelItem,
	ToggleControl
} from '@wordpress/components';

// Exports the post taxonomy panel.
const RewriteTagsPanel = ({
	attributes,
	setAttributes
}) => {
	const panelId = useInstanceId(RewriteTagsPanel);

	const { mapRewriteTags } = attributes;

	// Get viewable post types.
	const postTypes = useSelect((select) => {
		const types = select(coreStore).getPostTypes({ per_page: -1 });
		if (! types) {
			return [];
		}
		return Object.values(types).filter(
			postType => {
				// Check if post type is viewable
				if (true !== postType.viewable) {
					return false;
				}

				// Check if rewrite object exists and has slug property
				const rewrite = postType['x3p0-breadcrumbs/rewrite'];
				if (! rewrite || ! rewrite.slug) {
					return false;
				}

				// Check if slug contains '%' character
				return rewrite.slug.includes('%');
			}
		);
	}, []);

	const onRewriteTagChange = (postType, checked) => {
		const updatedRewriteTags = {
			...mapRewriteTags,
			[postType]: checked || false
		};

		// Remove empty values, but explicitly set `false` for the `post`
		// post type. This is because it's enabled by default, so we
		// need to explicitly tell the Breadcrumbs script on the PHP end
		// not to map tags for the post type.
		if (! checked && 'post' !== postType) {
			delete updatedRewriteTags[postType];
		}

		setAttributes({ mapRewriteTags: updatedRewriteTags });
	};

	// Reset handler for ToolsPanelItem
	const resetPanelItem = (postTypeSlug) => () => onRewriteTagChange(postTypeSlug, false);

	// Resets the post rewrite tags to no mapping. Explicitly set the `post`
	// post type.
	const resetPanel = () => setAttributes({
		mapRewriteTags: { post: false }
	});

	const rewriteTagPanelItems = postTypes.map((postType) => {
		const hasValue = !! mapRewriteTags[postType.slug];

		return (
			<ToolsPanelItem
				key={postType.slug}
				hasValue={() => hasValue}
				label={postType.labels.singular_name}
				onDeselect={resetPanelItem(postType.slug)}
				panelId={ panelId }
			>
				<ToggleControl
					label={ postType.labels.singular_name }
					checked={ mapRewriteTags[postType.slug] || false }
					onChange={ () => onRewriteTagChange(
						postType.slug,
						! mapRewriteTags[postType.slug]
					)}
					__nextHasNoMarginBottom={ true }
				/>
			</ToolsPanelItem>
		);
	}).filter(Boolean);

	return 0 !== postTypes.length && (
		<ToolsPanel
			label={__('Rewrite Tags', 'x3p0-breadcrumbs')}
			resetAll={ resetPanel }
			panelId={ panelId }
		>
			<div style={{ gridColumn: '1 / -1' }}>
				<RawHTML>
					{ sprintf(
						__('Map permalink rewrite tags, such as %1$s and %2$s, to breadcrumbs on single post views.', 'x3p0-breadcrumbs'),
						`<code>%category%</code>`,
						`<code>%author%</code>`
					)}
				</RawHTML>
			</div>
			{ rewriteTagPanelItems }
		</ToolsPanel>
	);
};

export default RewriteTagsPanel;
