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

// WordPress dependencies.
import { __ } from '@wordpress/i18n';
import {RichText, useBlockProps, useInnerBlocksProps} from '@wordpress/block-editor';
import { store as coreStore } from '@wordpress/core-data';
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
		homeIcon,
		justifyContent,
		linkTrailEnd,
		showHomeLabel,
		showTrailEnd,
		showTrailStart,
		showTrailingSeparator,
		separatorIcon
	},
	separatorColor,
	setSeparatorColor,
	style,
	setAttributes,
	isSelected
}) => {
	const blockProps = useBlockProps({
		className: clsx({
			'hide-home-label' : showTrailStart && ! showHomeLabel,
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

	const homeLabel = (
		<RichText
			tagName="span"
			className="wp-block-x3p0-breadcrumbs__crumb-label"
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

	// Built up as a list so the separator (rendered as real markup after
	// every crumb but the last, unless `showTrailingSeparator` is on)
	// can be inserted without repeating that condition at each crumb.
	const crumbs = [
		showTrailStart && {
			key: 'home',
			className: 'wp-block-x3p0-breadcrumbs__crumb wp-block-x3p0-breadcrumbs__crumb--home',
			content: (
				<CrumbLink>
					<CrumbIcon
						value={homeIcon}
						className="wp-block-x3p0-breadcrumbs__crumb-icon"
					/>
					{homeLabel}
				</CrumbLink>
			)
		},
		{
			key: 'ancestor',
			className: 'wp-block-x3p0-breadcrumbs__crumb wp-block-x3p0-breadcrumbs__crumb--post',
			content: (
				<CrumbLink>
					<span className="wp-block-x3p0-breadcrumbs__crumb-label">
						{__('Ancestor', 'x3p0-breadcrumbs')}
					</span>
				</CrumbLink>
			)
		},
		{
			key: 'parent',
			className: 'wp-block-x3p0-breadcrumbs__crumb wp-block-x3p0-breadcrumbs__crumb--post',
			content: (
				<CrumbLink>
					<span className="wp-block-x3p0-breadcrumbs__crumb-label">
						{__('Parent', 'x3p0-breadcrumbs')}
					</span>
				</CrumbLink>
			)
		},
		showTrailEnd && {
			key: 'current',
			className: 'wp-block-x3p0-breadcrumbs__crumb wp-block-x3p0-breadcrumbs__crumb--post',
			content: linkTrailEnd ? (
				<CrumbLink>
					<span className="wp-block-x3p0-breadcrumbs__crumb-label">
						{__('Current', 'x3p0-breadcrumbs')}
					</span>
				</CrumbLink>
			) : (
				<span className="wp-block-x3p0-breadcrumbs__crumb-content">
					<span className="wp-block-x3p0-breadcrumbs__crumb-label">
						{__('Current', 'x3p0-breadcrumbs')}
					</span>
				</span>
			)
		}
	].filter(Boolean);

	return (
		<nav {...innerBlocksProps}>
			<ol className="wp-block-x3p0-breadcrumbs__trail">
				{crumbs.map((crumb, index) => (
					<li key={crumb.key} className={crumb.className}>
						{crumb.content}
						{(index < crumbs.length - 1 || showTrailingSeparator) && (
							<CrumbIcon
								value={separatorIcon}
								options={SEPARATOR_ICONS}
								className="wp-block-x3p0-breadcrumbs__crumb-separator"
							/>
						)}
					</li>
				))}
			</ol>
		</nav>
	);
};

export default BlockContent;
