import metadata from '../block.json';

import classnames from 'classnames';

const { registerBlockType } = wp.blocks;
const { __ } = wp.i18n;
const {
	BlockControls,
	InspectorControls,
	JustifyContentControl,
	useBlockProps
} = wp.blockEditor;

const { useSelect } = wp.data;

const coreStore = wp.coreData.store;

const { PanelBody, ToggleControl } = wp.components;
const { withState } = wp.compose;

const ServerSideRender = wp.serverSideRender;

const preventDefault = ( event ) => event.preventDefault();

wp.domReady( () => {

	registerBlockType( metadata, {

		icon: <svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><g><rect fill="none" height="24" width="24"/></g><g><g><polygon points="15.5,5 11,5 16,12 11,19 15.5,19 20.5,12"/><polygon points="8.5,5 0,5 0,12 0,19 8.5,19 13.5,12"/></g></g></svg>,

		edit: function ( props ) {

			const { homeUrl } = useSelect(
				( select ) => {
					const {
						getUnstableBase, //site index
					} = select( coreStore );
					return {
						homeUrl: getUnstableBase()?.home,
					};
				},
				[ props.clientId ]
			);

			let blockProps = useBlockProps( {
				className: classnames( {
					'breadcrumbs' : true,
					[ `items-justified-${ props.attributes.itemsJustification }` ] : props.attributes.itemsJustification
				} )
			} );

			let justifyControls = [ 'left', 'center', 'right' ];

			return (
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
					<nav { ...blockProps }>
					<ul class="breadcrumbs__trail" itemscope="" itemtype="https://schema.org/BreadcrumbList">
					<li class="breadcrumbs__crumb breadcrumbs__crumb--home" itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem"><a href={ homeUrl } onClick={ preventDefault } itemprop="item"><span itemprop="name">{ __( 'Home' ) }</span></a><meta itemprop="position" content="1" /></li>
					<li class="breadcrumbs__crumb breadcrumbs__crumb--post" itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem"><a href="#" itemprop="item"><span itemprop="name">{ __( 'Example Page' ) }</span></a><meta itemprop="position" content="2" /></li>
					{ props.attributes.showTrailEnd && (
						<li class="breadcrumbs__crumb breadcrumbs__crumb--post" itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem"><span itemscope="" itemtype="https://schema.org/WebPage" itemprop="item"><span itemprop="name">{ __( 'Example Breadcrumb' ) }</span></span><meta itemprop="position" content="3" /></li>
					) }
					</ul>
					</nav>
				</>
			);
		},

		save: function() {
			return null;
		}

	} );

} );
