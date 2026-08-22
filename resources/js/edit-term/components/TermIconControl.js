/**
 * Term icon control component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// Internal dependencies.
import { IconLibraryModal, IconPreview } from '../../../blocks/breadcrumbs/components/ui';

// WordPress dependencies.
import { __, sprintf } from '@wordpress/i18n';
import {
	Button,
	__experimentalTruncate as Truncate
} from '@wordpress/components';
import { store as coreStore } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';

/**
 * Renders the breadcrumb icon field on the taxonomy screens' add and edit
 * forms — a button that opens the icon library, and the hidden input carrying
 * the choice out on the form's own post.
 *
 * The input is what makes this a form field rather than an editor: the value
 * is saved by `TermIconField` when WordPress saves the term, not written to the
 * REST API from here, so nothing is stored until the person submits the form
 * and nothing is stored twice when they do.
 *
 * The value is the term's own icon meta, which `Crumb\Type\Term` reads as its
 * `explicitIcon()` and so outranks the icon configured for the taxonomy. That
 * is what makes this an editorial choice about one term rather than a setting,
 * and why the field previews nothing when the meta is empty: the icon that
 * would render instead belongs to the taxonomy, and showing it here would read
 * as a choice already made for this term.
 * @param props
 * @returns {JSX.Element}
 */
export const TermIconControl = ({id, name, value: initialValue}) => {
	const [value, setValue] = useState(initialValue);
	const [isLibraryOpen, setLibraryOpen] = useState(false);

	// Adding a term on `edit-tags.php` goes through core's own Ajax handler,
	// which clears the form by hand rather than resetting it: `tags.js` empties
	// visible text inputs and textareas only, so the hidden input below keeps
	// its value and the next term would silently inherit this one's icon.
	//
	// The row core inserts into the term list is the nearest thing it offers to
	// a success signal, being written only once the response comes back clean.
	// Quick Edit rewrites rows in that same list, so the add form's own name
	// field is consulted as well — core empties it on a successful add and
	// leaves it alone otherwise, and it is empty by the time this runs, since a
	// mutation is observed only after the callback that caused it has finished.
	// What slips through is a Quick Edit made while the add form holds an icon
	// but no name, which costs an unsaved selection and nothing else.
	//
	// The edit form on `term.php` has neither a list nor an Ajax save, so there
	// is nothing to observe and nothing to reset.
	useEffect(() => {
		const list = document.getElementById('the-list');
		const nameField = document.getElementById('tag-name');

		if (! list || ! nameField) {
			return;
		}

		const observer = new MutationObserver(() => {
			if ('' === nameField.value) {
				setValue('');
			}
		});

		observer.observe(list, {childList: true});

		return () => observer.disconnect();
	}, []);

	// The chosen icon's own record, for the preview beside the button's label
	// and for the name the button reads out. Nothing is requested until the
	// term has an icon to resolve.
	const icon = useSelect(
		(select) => value
			? select(coreStore).getEntityRecord('root', 'icon', value)
			: null,
		[value]
	);

	const setIcon = (next) => {
		setValue(next);
		setLibraryOpen(false);
	};

	// The icon's registered name, standing in with the stored reference until
	// the record resolves — and for good if it never does, which says plainly
	// that the term is holding an icon nothing is registered under anymore.
	const label = value
		? (icon?.label ?? value)
		: __('Add an icon', 'x3p0-breadcrumbs');

	return (
		<>
			<input type="hidden" name={name} value={value}/>
			<Button
				id={id}
				className="x3p0-breadcrumbs-term-icon__toggle"
				variant="secondary"
				icon={icon?.content && <IconPreview content={icon.content}/>}
				onClick={() => setLibraryOpen(true)}
				aria-label={value
					? sprintf(
						// translators: %s: Name of the term's current breadcrumb icon.
						__('Change icon: %s', 'x3p0-breadcrumbs'),
						label
					)
					: undefined}
			>
				<Truncate numberOfLines={1}>
					{label}
				</Truncate>
			</Button>
			{isLibraryOpen && (
				<IconLibraryModal
					value={value}
					title={__('Breadcrumb Icon', 'x3p0-breadcrumbs')}
					description={__('Pick an icon for this breadcrumb item.', 'x3p0-breadcrumbs')}
					onSelect={setIcon}
					onReset={() => setIcon('')}
					onRequestClose={() => setLibraryOpen(false)}
				/>
			)}
		</>
	);
};
