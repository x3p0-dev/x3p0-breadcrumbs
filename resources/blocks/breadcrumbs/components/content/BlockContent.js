/**
 * Block content.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Internal dependencies.
import { SEPARATOR_ICONS } from '../../utils/constants';
import { ICON_OPTION_KEYS, POST_TYPE_ICON_OPTION_KEYS } from '../../utils/icon-options';
import { META_KEYS } from '../../utils/meta-keys';

// WordPress dependencies.
import { __ } from '@wordpress/i18n';
import {RichText, useBlockProps, useInnerBlocksProps} from '@wordpress/block-editor';
import { store as coreStore, useEntityProp } from '@wordpress/core-data';
import { store as editorStore } from '@wordpress/editor';
import { safeHTML } from '@wordpress/dom';
import { useSelect } from '@wordpress/data';

// Third-party dependencies.
import clsx from 'clsx';

/**
 * Prevents navigation when a faux crumb link is clicked in the editor
 * canvas — these links exist only to preview the trail's markup and have
 * no real `href` to follow.
 * @param {Event} event
 * @returns {void}
 */
const preventDefault = (event) => event.preventDefault();

/**
 * Faux crumb link for the content canvas.
 * @param children
 * @returns {JSX.Element}
 * @constructor
 */
const CrumbLink = ({ children }) => (
	<a className="wp-block-x3p0-breadcrumbs__crumb-content" href="#breadcrumb-link" onClick={preventDefault}>
		{children}
	</a>
);

/**
 * Faux crumb label for the content canvas. Mirrors `Html::renderCrumbLabel()`
 * — the label always renders, but gains a visually-hidden modifier class
 * when `hidden` is true rather than being omitted.
 * @param children
 * @param hidden
 * @returns {JSX.Element}
 * @constructor
 */
const CrumbLabel = ({ children, hidden }) => (
	<span
		className={clsx('wp-block-x3p0-breadcrumbs__crumb-label', {
			'wp-block-x3p0-breadcrumbs__crumb-label--hidden': hidden
		})}
	>
		{children}
	</span>
);

/**
 * Renders a home or separator icon as real, `aria-hidden` markup — a
 * built-in SVG/emoji from `options`, or an SVG fetched from the registered
 * icon library (a `value` containing a `/`) — instead of driving it purely
 * through a CSS class/mask, matching how WordPress core itself renders
 * registered icons.
 * @param props
 * @returns {JSX.Element|null}
 */
const CrumbIcon = ({ value, options = [], className }) => {
	const builtIn = value && options.find((option) => option.value === value);

	const libraryIcon = useSelect(
		(select) => ! builtIn && value?.includes('/')
			? select(coreStore).getEntityRecord('root', 'icon', value)
			: null,
		[builtIn, value]
	);

	if (builtIn) {
		return (
			<span className={className} aria-hidden="true">
				{builtIn.icon}
			</span>
		);
	}

	if (libraryIcon?.content) {
		return (
			<span
				className={className}
				aria-hidden="true"
				dangerouslySetInnerHTML={{__html: safeHTML(libraryIcon.content)}}
			/>
		);
	}

	return null;
};

/**
 * Creates the Breadcrumbs block content to be rendered in the editor.
 * @param props
 * @returns {JSX.Element}
 * @constructor
 */
const BlockContent = ({
	attributes: {
		customSeparatorColor,
		labels = {},
		icons = {},
		iconVisibility,
		justifyContent,
		labelVisibility,
		linkTrailEnd,
		showSeparator = true,
		showTrailEnd,
		showTrailStart,
		showTrailingSeparator
	},
	separatorColor,
	setSeparatorColor,
	style,
	setAttributes,
	isSelected
}) => {
	// Mirrors `Crumb::getIcon()`: an option key resolves to the user's
	// chosen icon from the `icons` attribute, falling back to the option's
	// registered default (passed from PHP via `IconOptions::forBlock()`).
	// Whether an icon actually renders is controlled separately by
	// `iconVisibility`, not by these values.
	//
	// noinspection JSUnresolvedVariable
	const resolveIcon = (key) => icons[key]
		|| window.x3p0Breadcrumbs?.iconOptions?.find((option) => key === option.key)?.icon
		|| '';

	// The generic Ancestor/Parent/Current placeholder crumbs stand in for a
	// post, so they use the real Page single-post icon.
	const pageIcon = resolveIcon(ICON_OPTION_KEYS.POST_TYPE_PAGE);

	// The post the editor has open, if it has one. The block is edited in a
	// template as often as on a post, and a template has no single post the
	// trail could end on, so `viewable` is what says whether there is a real
	// thing here to stand at the end of it — the same question `IconRow` asks.
	//
	// The post is named outright rather than left to the entity context, which
	// a block in the site editor's canvas is not guaranteed to inherit from the
	// page being edited. What the editor store reports is true either way.
	const {postType, postId, isViewable} = useSelect((select) => {
		const {getCurrentPostType, getCurrentPostId} = select(editorStore);
		const type = getCurrentPostType();

		return {
			postType: type,
			postId: getCurrentPostId(),
			isViewable: Boolean(type && select(coreStore).getPostType(type)?.viewable)
		};
	}, []);

	const [postTitle] = useEntityProp('postType', postType, 'title', postId);
	const [postMeta]  = useEntityProp('postType', postType, 'meta', postId);

	// What the trail's last crumb stands for: the open post, when the block is
	// being edited on one and it has a title yet, and the generic placeholder
	// otherwise. Its icon reads the way the crumb resolves one — the post's own
	// icon meta, which `Crumb\Type\Post::explicitIcon()` ranks above everything
	// else, then the icon for its post type, then the generic page icon the
	// placeholder crumbs stand on.
	const currentLabel = (isViewable && postTitle)
		|| __('Current', 'x3p0-breadcrumbs');

	const postTypeIcon = isViewable && POST_TYPE_ICON_OPTION_KEYS[postType]
		? resolveIcon(POST_TYPE_ICON_OPTION_KEYS[postType])
		: '';

	const currentIcon = (isViewable && postMeta?.[META_KEYS.icon])
		|| postTypeIcon
		|| pageIcon;

	const homeIconValue = resolveIcon(ICON_OPTION_KEYS.HOME);

	const separatorIconValue = resolveIcon(ICON_OPTION_KEYS.SEPARATOR);

	// Mirrors `Html::isCrumbIconVisible()`: which crumbs show an icon depends
	// on their position in the trail as accumulated, not on their kind and
	// not on what survives to the output. Hiding the home crumb suppresses
	// its output without taking it out of the trail, so "Ancestor" stays the
	// second crumb and "first" keeps pointing at the home crumb that is no
	// longer drawn — matching `CrumbCollection::isFirst()`/`isLast()`, which
	// report position in the accumulated collection regardless of whether
	// `Markup::isCrumbRenderable()` goes on to skip the crumb.
	const isCrumbIconVisible = (index, total) => {
		switch (iconVisibility) {
			case 'all':          return true;
			case 'all-but-last': return index !== total - 1;
			case 'first':        return 0 === index;
			default:             return false;
		}
	};

	// Mirrors `Html::isCrumbLabelHidden()`: a crumb's label is only ever
	// actually hidden when its icon is showing in its place — otherwise the
	// crumb would have nothing visible or accessible standing in for it, so
	// the label is forced to show regardless of `labelVisibility`.
	const isCrumbLabelHidden = (index, total, iconVisible) => {
		const hide = (() => {
			switch (labelVisibility) {
				case 'all-but-first': return 0 === index;
				case 'last':          return index !== total - 1;
				case 'none':          return true;
				default:              return false;
			}
		})();

		return hide && iconVisible;
	};

	const blockProps = useBlockProps({
		className: clsx({
			[`is-content-justification-${justifyContent}`] : justifyContent
		}),
		style: {
			...style,
			'--x3p0-breadcrumbs--color--separator': separatorColor.slug && separatorColor.color
				? `var(--wp--preset--color--${separatorColor.slug}, ${separatorColor.color})`
				: customSeparatorColor
		}
	});

	// We must use inner blocks props for layout styles to work properly in
	// the admin, even though this block doesn't have nested blocks.
	const innerBlocksProps = useInnerBlocksProps(blockProps);

	// We need a default home label value for non-editing contexts when
	// there's no saved value. This is because `RichText` will not show the
	// placeholder in those cases. For example, on the Site Editor or
	// Templates screens.
	const homeValue = labels?.home
		? labels.home
		: isSelected ? '' : __('Home', 'x3p0-breadcrumbs')

	const renderHomeLabel = (hidden) => (
		<RichText
			tagName="span"
			className={clsx('wp-block-x3p0-breadcrumbs__crumb-label', {
				'wp-block-x3p0-breadcrumbs__crumb-label--hidden': hidden
			})}
			aria-label={__('Home breadcrumb label', 'x3p0-breadcrumbs')}
			placeholder={__('Home', 'x3p0-breadcrumbs')}
			value={homeValue}
			multiline={false}
			disableLineBreaks={true}
			onChange={(value) => {
				const updatedLabels = {...labels};

				if (value) {
					updatedLabels.home = value;
				} else {
					delete updatedLabels.home;
				}

				setAttributes({ labels: updatedLabels });
			}}
			allowedFormats={[]}
			withoutInteractiveFormatting={true}
		/>
	);

	// The whole trail, including the crumbs the block is configured to hide:
	// `showTrailStart`/`showTrailEnd` keep a crumb out of the output without
	// taking it out of the trail, so every crumb keeps its place here and
	// carries a `visible` flag instead — the editor equivalent of PHP
	// iterating the full `CrumbCollection` and letting
	// `Markup::isCrumbRenderable()` skip crumbs as it goes. Each crumb's
	// `content` is a function of whether its icon is visible, since that
	// depends on the crumb's position in the trail (see
	// `isCrumbIconVisible()`) rather than being knowable up front.
	const crumbs = [
		{
			visible: showTrailStart,
			key: 'home',
			className: 'wp-block-x3p0-breadcrumbs__crumb wp-block-x3p0-breadcrumbs__crumb--home',
			content: (showIcon, labelHidden) => (
				<CrumbLink>
					{showIcon && (
						<CrumbIcon
							value={homeIconValue}
							className="wp-block-x3p0-breadcrumbs__crumb-icon"
						/>
					)}
					{renderHomeLabel(labelHidden)}
				</CrumbLink>
			)
		},
		{
			visible: true,
			key: 'ancestor',
			className: 'wp-block-x3p0-breadcrumbs__crumb wp-block-x3p0-breadcrumbs__crumb--post',
			content: (showIcon, labelHidden) => (
				<CrumbLink>
					{showIcon && (
						<CrumbIcon
							value={pageIcon}
							className="wp-block-x3p0-breadcrumbs__crumb-icon"
						/>
					)}
					<CrumbLabel hidden={labelHidden}>
						{__('Ancestor', 'x3p0-breadcrumbs')}
					</CrumbLabel>
				</CrumbLink>
			)
		},
		{
			visible: true,
			key: 'parent',
			className: 'wp-block-x3p0-breadcrumbs__crumb wp-block-x3p0-breadcrumbs__crumb--post',
			content: (showIcon, labelHidden) => (
				<CrumbLink>
					{showIcon && (
						<CrumbIcon
							value={pageIcon}
							className="wp-block-x3p0-breadcrumbs__crumb-icon"
						/>
					)}
					<CrumbLabel hidden={labelHidden}>
						{__('Parent', 'x3p0-breadcrumbs')}
					</CrumbLabel>
				</CrumbLink>
			)
		},
		{
			visible: showTrailEnd,
			key: 'current',
			className: 'wp-block-x3p0-breadcrumbs__crumb wp-block-x3p0-breadcrumbs__crumb--post',
			content: (showIcon, labelHidden) => linkTrailEnd ? (
				<CrumbLink>
					{showIcon && (
						<CrumbIcon
							value={currentIcon}
							className="wp-block-x3p0-breadcrumbs__crumb-icon"
						/>
					)}
					<CrumbLabel hidden={labelHidden}>
						{currentLabel}
					</CrumbLabel>
				</CrumbLink>
			) : (
				<span className="wp-block-x3p0-breadcrumbs__crumb-content">
					{showIcon && (
						<CrumbIcon
							value={currentIcon}
							className="wp-block-x3p0-breadcrumbs__crumb-icon"
						/>
					)}
					<CrumbLabel hidden={labelHidden}>
						{currentLabel}
					</CrumbLabel>
				</span>
			)
		}
	];

	return (
		<nav {...innerBlocksProps}>
			<ol className="wp-block-x3p0-breadcrumbs__trail">
				{crumbs.map((crumb, index) => {
					if (! crumb.visible) {
						return null;
					}

					const iconVisible = isCrumbIconVisible(index, crumbs.length);
					const labelHidden = isCrumbLabelHidden(index, crumbs.length, iconVisible);

					// Mirrors `Markup::isLastRenderedCrumb()`: the
					// separator closes off every crumb but the last one
					// that actually renders, which is not necessarily the
					// last crumb in the trail.
					const isLastRendered = ! crumbs.slice(index + 1).some(
						(next) => next.visible
					);

					return (
						<li key={crumb.key} className={crumb.className}>
							{crumb.content(iconVisible, labelHidden)}
							{showSeparator && (! isLastRendered || showTrailingSeparator) && (
								<CrumbIcon
									value={separatorIconValue}
									options={SEPARATOR_ICONS}
									className="wp-block-x3p0-breadcrumbs__crumb-separator"
								/>
							)}
						</li>
					);
				})}
			</ol>
		</nav>
	);
};

export default BlockContent;
