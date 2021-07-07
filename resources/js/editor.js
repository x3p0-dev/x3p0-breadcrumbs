
// Import block metadata and settings.
import * as breadcrumbs from './block/index';

// WP dependencies.
const { registerBlockType } = wp.blocks;

// Register block types.
wp.domReady( () => {
	registerBlockType( breadcrumbs.metadata, breadcrumbs.settings );
} );
