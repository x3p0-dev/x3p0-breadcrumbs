/**
 * Registers the plugin's UI on the term editing screens.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Import dependencies.
import { createRoot } from '@wordpress/element';

// Import the components.
import { TermIconControl } from './components/TermIconControl';

// `TermIconField` loads this script only on a taxonomy screen it has already
// decided the field belongs on, and renders one mount node into whichever of
// the two term forms that screen carries — so there is nothing left to ask
// here beyond where the nodes are. The script loads in the footer, by which
// point the form is in the document.
document.querySelectorAll('.x3p0-breadcrumbs-term-icon').forEach((node) => {
	createRoot(node).render(
		<TermIconControl
			id={node.dataset.id}
			name={node.dataset.name}
			value={node.dataset.value}
		/>
	);
});
