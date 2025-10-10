/**
 * Handles the edit component for the breadcrumbs block.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Internal dependencies.
import Inspector from './inspector';
import Toolbar from './toolbar';
import Trail from './element-trail';

// WordPress dependencies.
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

// Third-party dependencies.
import clsx from 'clsx';

// Exports the breadcrumbs block type edit function.
export default (props) => {
	const {
		homePrefix,
		homePrefixType,
		justifyContent,
		showHomeLabel,
		showTrailStart,
		separator,
		separatorType
	} = props.attributes;

	// Get the blockProps and add custom classes.
	const blockProps = useBlockProps({
		className: clsx({
			'breadcrumbs': true,
			[ `has-home-${homePrefixType}-${ homePrefix }`   ] : showTrailStart && homePrefixType && homePrefix,
			[ 'hide-home-label'                              ] : showTrailStart && ! showHomeLabel,
			[ `has-sep-${separatorType}-${ separator }`      ] : separatorType && separator,
			[ `is-content-justification-${ justifyContent }` ] : justifyContent
		})
	});

	// Need inner block props for layout styles to work properly in the admin.
	const innerBlockProps = useInnerBlocksProps(blockProps);

	// Return the final block edit component.
	return (
		<>
			<Toolbar {...props}/>
			<Inspector {...props}/>
			<nav {...innerBlockProps}>
				<Trail {...props}/>
			</nav>
		</>
	);
};
