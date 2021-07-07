
// Node modules dependencies.
import classnames from 'classnames';

// WP dependencies.
const coreStore = wp.coreData.store;

const { __ }        = wp.i18n;
const { useSelect } = wp.data;

const { PanelBody, ToggleControl } = wp.components;

const {
	BlockControls,
	InspectorControls,
	JustifyContentControl,
	useBlockProps
} = wp.blockEditor;

// Prevent breadcrumb link events when users click them.
const preventDefault = ( event ) => event.preventDefault();

// Define allowed justification controls.
const justifyControls = [ 'left', 'center', 'right' ];

// Exports the breadcrumbs block type edit function.
export default function BreadcrumbsEdit( props ) {

	// Gets the home URL from WordPress.
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

	// Get the blockProps and add custom classes.
	let blockProps = useBlockProps( {
		className: classnames( {
			'breadcrumbs' : true,
			[ `items-justified-${ props.attributes.itemsJustification }` ] : props.attributes.itemsJustification
		} )
	} );

	// Return elements and controls.
	return (
		<>
		<BlockControls group="block">
			<JustifyContentControl
				allowedControls={ justifyControls }
				value={ props.attributes.itemsJustification }
				onChange={ ( value ) =>
					props.setAttributes( {
						itemsJustification: value
					} )
				}
				popoverProps={ {
					position: 'bottom right',
					isAlternate: true,
				} }
			/>
		</BlockControls>
		<InspectorControls>
			<PanelBody title={
				__( 'Breadcrumb settings', 'x3p0-breadcrumbs' )
			}>
			<ToggleControl
				label={ __( 'Show on homepage', 'x3p0-breadcrumbs' ) }
				help={
					props.attributes.showOnHomepage
						? __( 'Breadcrumbs display on the homepage.', 'x3p0-breadcrumbs' )
						: __( 'Breadcrumbs hidden on the homepage.', 'x3p0-breadcrumbs' )
				}
				checked={ props.attributes.showOnHomepage }
				onChange={ () =>
					props.setAttributes( {
						showOnHomepage: ! props.attributes.showOnHomepage
					} )
				}
			/>
			<ToggleControl
				label={ __( 'Show last breadcrumb', 'x3p0-breadcrumbs' ) }
				help={
					props.attributes.showTrailEnd
						? __( 'Current page item is shown.', 'x3p0-breadcrumbs' )
						: __( 'Current page item is hidden.', 'x3p0-breadcrumbs' )
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
				<li class="breadcrumbs__crumb breadcrumbs__crumb--home" itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem">
					<a href={ homeUrl } onClick={ preventDefault } class="breadcrumbs__crumb-content" itemprop="item">
						<span itemprop="name">{ __( 'Home', 'x3p0-breadcrumbs' ) }</span>
					</a>
					<meta itemprop="position" content="1" />
				</li>
				<li class="breadcrumbs__crumb breadcrumbs__crumb--post" itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem">
					<a href="#" onClick={ preventDefault } class="breadcrumbs__crumb-content" itemprop="item">
						<span itemprop="name">{ __( 'Parent Crumb', 'x3p0-breadcrumbs' ) }</span>
					</a>
					<meta itemprop="position" content="2" />
				</li>
				{ props.attributes.showTrailEnd && (
					<li class="breadcrumbs__crumb breadcrumbs__crumb--post" itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem">
						<span class="breadcrumbs__crumb-content" itemscope="" itemtype="https://schema.org/WebPage" itemprop="item">
							<span itemprop="name">{ __( 'Current Crumb', 'x3p0-breadcrumbs' ) }</span>
						</span>
						<meta itemprop="position" content="3" />
					</li>
				) }
			</ul>
		</nav>
		</>
	);
}
