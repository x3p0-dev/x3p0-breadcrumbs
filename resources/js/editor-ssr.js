import metadata from '../../public/block.json';

import classnames from 'classnames';

const { registerBlockType } = wp.blocks;
const { __ } = wp.i18n;
const {
	BlockControls,
	InspectorControls,
	JustifyContentControl,
	useBlockProps
} = wp.blockEditor;

const { PanelBody, ToggleControl } = wp.components;
const { withState } = wp.compose;

const ServerSideRender = wp.serverSideRender;

wp.domReady( () => {

	registerBlockType( metadata, {

		icon: <svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><g><rect fill="none" height="24" width="24"/></g><g><g><polygon points="15.5,5 11,5 16,12 11,19 15.5,19 20.5,12"/><polygon points="8.5,5 0,5 0,12 0,19 8.5,19 13.5,12"/></g></g></svg>,

		edit: function ( props ) {
			let blockProps = useBlockProps( {
				className: classnames( {
					[ `items-justified-${ props.attributes.itemsJustification }` ] : props.attributes.itemsJustification
				} )
			} );

			let justifyControls = [ 'left', 'center', 'right' ];

			return (
				<div { ...blockProps }>
					<>
						<BlockControls group="block">
						<JustifyContentControl
							allowedControls={ justifyControls }
							value={ props.attributes.itemsJustification }
							onChange={ ( value ) =>
								props.setAttributes( { itemsJustification: value } )
							}
							popoverProps={ {
								position: 'bottom right',
								isAlternate: true,
							} }
						/>
						</BlockControls>
						<InspectorControls>
							<PanelBody title={ __( 'Breadcrumb settings' ) }>
							<ToggleControl
								label={ __( 'Show on homepage', 'breadcrumbs' ) }
								help={
									props.attributes.showOnHomepage
										? __( 'Breadcrumbs display on the homepage.', 'breadcrumbs' )
										: __( 'Breadcrumbs hidden on the homepage.', 'breadcrumbs' )
								}
								checked={ props.attributes.showOnHomepage }
								onChange={ () =>
									props.setAttributes( {
										showOnHomepage: ! props.attributes.showOnHomepage
									} )
								}
							/>
							<ToggleControl
								label={ __( 'Show last breadcrumb', 'breadcrumbs' ) }
								help={
									props.attributes.showTrailEnd
										? __( 'Current page item is shown.', 'breadcrumbs' )
										: __( 'Current page item is hidden.', 'breadcrumbs' )
								}
								checked={ props.attributes.showTrailEnd }
								onChange={ () =>
									props.setAttributes( {
										showTrailEnd: ! props.attributes.showTrailEnd
									} )
								}
							/>
							</PanelBody>
						</InspectorControls>
					</>
					<ServerSideRender
						block="x3p0/breadcrumbs"
						attributes={ props.attributes }
						className="wp-block-x3p0-breadcrumbs__editor-wrap"
					/>
				</div>
			);
		},

		save: function() {
			return null;
		}

	} );

} );
