/**
 * Returns the breadcrumb trail element.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// WordPress dependencies.
import { __ } from '@wordpress/i18n';
import { RichText } from '@wordpress/block-editor';

// Prevent breadcrumb link events when users click them.
const preventDefault = (event) => event.preventDefault();

// Exports the breadcrumbs block type edit function.
export default ({ attributes, setAttributes, isSelected }) => {
	const {
		labels,
		showTrailStart,
		showTrailEnd,
	} = attributes;

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
			className="breadcrumbs__crumb-label"
			aria-label={ __('Home breadcrumb label', 'x3p0-breadcrumbs') }
			placeholder={ __('Home', 'x3p0-breadcrumbs') }
			value={ homeValue }
			multiline={ false }
			onChange={ (value) => {
				const updatedLabels = {
					...labels,
					home: value
				};

				// Remove empty values
				if (! value) {
					delete updatedLabels.home;
				}

				setAttributes({ labels: updatedLabels });
			}}
			allowedFormats={ [] }
			withoutInteractiveFormatting={ true }
		/>
	);

	// Builds a preview breadcrumb trail for the editor.
	return (
		<ol className="breadcrumbs__trail">
			{ showTrailStart && (
				<li className="breadcrumbs__crumb breadcrumbs__crumb--home">
					<a
						href="#breadcrumbs-pseudo-link"
						onClick={ preventDefault }
						className="breadcrumbs__crumb-content"
					>
						{ homeLabel }
					</a>
				</li>
			)}
			<li className="breadcrumbs__crumb breadcrumbs__crumb--post">
				<a
					href="#breadcrumbs-pseudo-link"
					onClick={ preventDefault }
					className="breadcrumbs__crumb-content"
				>
					<span className="breadcrumbs__crumb-label">
						{ __('Parent Page', 'x3p0-breadcrumbs') }
					</span>
				</a>
			</li>
			{ showTrailEnd && (
				<li className="breadcrumbs__crumb breadcrumbs__crumb--post">
					<span className="breadcrumbs__crumb-content">
						<span className="breadcrumbs__crumb-label">
							{ __('Current Page', 'x3p0-breadcrumbs') }
						</span>
					</span>
				</li>
			)}
		</ol>
	);
};
