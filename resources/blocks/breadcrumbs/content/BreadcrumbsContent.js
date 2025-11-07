/**
 * Returns the breadcrumbs block content.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// WordPress dependencies.
import { __ } from '@wordpress/i18n';
import {RichText, useBlockProps, useInnerBlocksProps} from '@wordpress/block-editor';

// Third-party dependencies.
import clsx from 'clsx';

/**
 * Prevent breadcrumb link events when users click them.
 * @param event
 * @returns {*}
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
 * Creates the Breadcrumbs block content to be rendered in the editor.
 * @param props
 * @returns {JSX.Element}
 * @constructor
 */
const BreadcrumbsContent = ({
	attributes: {
		labels = {},
		homeIcon,
		justifyContent,
		linkTrailEnd,
		showHomeLabel,
		showTrailEnd,
		showTrailStart,
		separatorIcon
	},
	setAttributes,
	isSelected
}) => {
	const blockProps = useBlockProps({
		className: clsx({
			'breadcrumbs': true,
			[`has-home-${homeIcon}`] : showTrailStart && homeIcon,
			['hide-home-label'] : showTrailStart && ! showHomeLabel,
			[`has-sep-${separatorIcon}`] : separatorIcon,
			[`is-content-justification-${justifyContent}`] : justifyContent
		})
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

	return (
		<nav {...innerBlocksProps}>
			<ol className="wp-block-x3p0-breadcrumbs__trail">
				{showTrailStart && (
					<li className="wp-block-x3p0-breadcrumbs__crumb wp-block-x3p0-breadcrumbs__crumb--home">
						<CrumbLink>
							{homeLabel}
						</CrumbLink>
					</li>
				)}
				<li className="wp-block-x3p0-breadcrumbs__crumb wp-block-x3p0-breadcrumbs__crumb--post">
					<CrumbLink>
						<span className="wp-block-x3p0-breadcrumbs__crumb-label">
							{__('Ancestor', 'x3p0-breadcrumbs')}
						</span>
					</CrumbLink>
				</li>
				<li className="wp-block-x3p0-breadcrumbs__crumb wp-block-x3p0-breadcrumbs__crumb--post">
					<CrumbLink>
						<span className="wp-block-x3p0-breadcrumbs__crumb-label">
							{__('Parent', 'x3p0-breadcrumbs')}
						</span>
					</CrumbLink>
				</li>
				{showTrailEnd && (
					<li className="wp-block-x3p0-breadcrumbs__crumb wp-block-x3p0-breadcrumbs__crumb--post">
						{linkTrailEnd ? (
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
						)}
					</li>
				)}
			</ol>
		</nav>
	);
};

export default BreadcrumbsContent;
