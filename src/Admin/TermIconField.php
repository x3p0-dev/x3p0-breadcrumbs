<?php

/**
 * Term icon field class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Admin;

use WP_Screen;
use WP_Taxonomy;
use WP_Term;
use X3P0\Breadcrumbs\Crumb\Type\Term;
use X3P0\Breadcrumbs\Meta\MetaKey;
use X3P0\Breadcrumbs\Meta\MetaRegistrar;
use X3P0\Breadcrumbs\Packages\Framework\Contracts\Bootable;

/**
 * Adds a breadcrumb icon field to the taxonomy screens' add and edit forms, and
 * writes what it collects to {@see MetaKey::Icon} on the term — the value
 * {@see Term} reads as its explicit icon, outranking the icon configured for the
 * taxonomy as a whole.
 *
 * The field is a mount point rather than markup: a hidden input and the icon
 * library picker are rendered into it by the script {@see TermIconAssets} loads, so
 * a term is picked from the same library, in the same modal, as everything else
 * in the plugin. The value still rides out on the form's own post, which leaves
 * WordPress's save path in charge and keeps this from writing terms behind the
 * screen's back.
 */
final class TermIconField implements Bootable
{
	/**
	 * Nonce action for the field.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const NONCE_ACTION = 'x3p0-breadcrumbs-term-icon';

	/**
	 * Request key holding the field's nonce.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const NONCE_NAME = 'x3p0-breadcrumbs-term-icon-nonce';

	/**
	 * ID given to the control the label points at, which the script puts on
	 * the button it renders. One value covers both forms because they never
	 * share a page: `edit-tags.php` carries the add form and `term.php` the
	 * edit form.
	 *
	 * @var  string
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const FIELD_ID = 'x3p0-breadcrumbs-term-icon';

	/**
	 * Stores the screens' assets, enqueued only once there is a field to
	 * render.
	 */
	public function __construct(private readonly TermIconAssets $assets)
	{}

	/**
	 * @inheritDoc
	 */
	public function boot(): void
	{
		if (! is_admin()) {
			return;
		}

		add_action('current_screen', $this->addScreenHooks(...));

		// The generic term hooks rather than the `{$taxonomy}_`-prefixed
		// ones, because the add form posts through `admin-ajax.php`, where
		// there is no screen to read a taxonomy off and enumerating every
		// public taxonomy up front would be the only way to know which
		// prefixed hooks to add. What that costs is precision — these fire
		// for any term written anywhere, by REST, WP-CLI, or an importer —
		// which is what {@see self::save()} spends its guards buying back.
		add_action('created_term', $this->save(...), 10, 3);
		add_action('edited_term', $this->save(...), 10, 3);
	}

	/**
	 * Hooks the field onto the screen's own forms, once the screen turns out
	 * to be a taxonomy screen for a taxonomy that takes an icon. The taxonomy
	 * is read from the screen rather than the request because that is where
	 * both screens have already resolved it, and it is the same value the
	 * `{$taxonomy}_add_form_fields` and `{$taxonomy}_edit_form_fields` hook
	 * names are built from.
	 */
	private function addScreenHooks(WP_Screen $screen): void
	{
		if (! in_array($screen->base, ['edit-tags', 'term'], true)) {
			return;
		}

		$taxonomy = (string) $screen->taxonomy;

		if (! $this->supports($taxonomy)) {
			return;
		}

		add_action('admin_enqueue_scripts', $this->assets->enqueue(...));
		add_action("{$taxonomy}_add_form_fields", $this->renderAddField(...));
		add_action("{$taxonomy}_edit_form_fields", $this->renderEditField(...));
	}

	/**
	 * Renders the field on the "Add New" form, which stacks `div.form-field`
	 * blocks rather than laying its rows out in a table.
	 */
	private function renderAddField(): void
	{
		echo '<div class="form-field">';

		$this->renderLabel();
		$this->renderControl('');
		$this->renderDescription();

		echo '</div>';
	}

	/**
	 * Renders the field as a row on the edit form, which is a `form-table`
	 * and so wants a `tr` rather than a `div`.
	 */
	private function renderEditField(WP_Term $term): void
	{
		echo '<tr class="form-field">';

		echo '<th scope="row">';
		$this->renderLabel();
		echo '</th>';

		echo '<td>';
		$this->renderControl((string) get_term_meta($term->term_id, MetaKey::Icon->value, true));
		$this->renderDescription();
		echo '</td>';

		echo '</tr>';
	}

	/**
	 * Renders the field's label, pointing at the button the script renders —
	 * which is a labelable element, so the association is the same one the
	 * rest of the form's fields make.
	 */
	private function renderLabel(): void
	{
		printf(
			'<label for="%s">%s</label>',
			esc_attr(self::FIELD_ID),
			esc_html__('Breadcrumb Icon', 'x3p0-breadcrumbs')
		);
	}

	/**
	 * Renders the control: the nonce the save path checks for, and an empty
	 * node carrying everything the script needs to render into it — the ID the
	 * label points at, the name the value posts under, and the icon already
	 * stored, if any.
	 */
	private function renderControl(string $value): void
	{
		wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME);

		printf(
			'<div class="x3p0-breadcrumbs-term-icon" data-id="%s" data-name="%s" data-value="%s"></div>',
			esc_attr(self::FIELD_ID),
			esc_attr(MetaKey::Icon->value),
			esc_attr($value)
		);
	}

	/**
	 * Renders the field's description, which says what the icon is for and
	 * what happens without one — the taxonomy's own icon, configured on the
	 * block, is what the trail falls back to.
	 */
	private function renderDescription(): void
	{
		printf(
			'<p class="description">%s</p>',
			esc_html__('Shown beside this term wherever it appears in a breadcrumb trail. Without one, the icon set for the taxonomy is used.', 'x3p0-breadcrumbs')
		);
	}

	/**
	 * Stores the posted icon on the term.
	 *
	 * Everything above the write is here to establish that this term, in this
	 * request, is the one the form was submitted for. The hooks it runs on are
	 * fired by `wp_insert_term()` and `wp_update_term()`, which any code may
	 * call at any time, so a request carrying the field could otherwise write
	 * its icon onto some unrelated term created alongside it — and a request
	 * carrying no field at all, a REST update or a WP-CLI run, would read
	 * `$_POST` as an instruction to blank the icon out.
	 *
	 * The value is passed on as posted. Whether it names a registered icon is
	 * settled by the sanitize callback {@see MetaRegistrar} registered with the
	 * meta key itself, which `update_term_meta()` runs on the way in, so this
	 * has no whitelist of its own to fall out of step with.
	 */
	private function save(int $termId, int $termTaxonomyId, string $taxonomy): void
	{
		$nonce = isset($_POST[self::NONCE_NAME])
			? sanitize_text_field(wp_unslash($_POST[self::NONCE_NAME]))
			: '';

		if (! wp_verify_nonce($nonce, self::NONCE_ACTION)) {
			return;
		}

		// Both forms name themselves in `action` and carry the taxonomy they
		// belong to; the edit form also names the term it is editing, which
		// settles which term this is outright. The add form has no term to
		// name — there is none until the moment this hook fires — so its own
		// submission is as far as the question can be taken there.
		$action = isset($_POST['action'])
			? sanitize_key(wp_unslash($_POST['action']))
			: '';

		$postedTaxonomy = isset($_POST['taxonomy'])
			? sanitize_key(wp_unslash($_POST['taxonomy']))
			: '';

		$isSubmission = match ($action) {
			'add-tag'   => true,
			'editedtag' => $termId === absint(wp_unslash($_POST['tag_ID'] ?? 0)),
			default     => false
		};

		if (! $isSubmission || $postedTaxonomy !== $taxonomy || ! $this->supports($taxonomy)) {
			return;
		}

		if (! isset($_POST[MetaKey::Icon->value]) || ! current_user_can('edit_term', $termId)) {
			return;
		}

		update_term_meta(
			$termId,
			MetaKey::Icon->value,
			sanitize_text_field(wp_unslash($_POST[MetaKey::Icon->value]))
		);
	}

	/**
	 * Whether terms of the given taxonomy can take an icon, which comes down
	 * to whether they can appear in a trail at all: a taxonomy that is not
	 * public has no term archives to build one for.
	 */
	private function supports(string $taxonomy): bool
	{
		return is_taxonomy_viewable($taxonomy);
	}
}
