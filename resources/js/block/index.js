
// Internal dependencies.
import edit from './edit';
import icon from './icon';
import save from './save';

// Export the block type metadata.
export { default as metadata } from '../../block.json';

// Export the block type settings.
export const settings = {
	icon: icon,
	edit,
	save
};
