<?php

/**
 * HTML markup class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Markup\Type;

use X3P0\Breadcrumbs\Crumb\Crumb;
use X3P0\Breadcrumbs\Crumb\CrumbCollection;
use X3P0\Breadcrumbs\Icon\IconResolver;
use X3P0\Breadcrumbs\Markup\IconVisibility;
use X3P0\Breadcrumbs\Markup\LabelVisibility;
use X3P0\Breadcrumbs\Markup\Markup;
use X3P0\Breadcrumbs\Markup\MarkupBlockOption;
use X3P0\Breadcrumbs\Markup\MarkupConfig;
use X3P0\Breadcrumbs\Markup\MarkupType;
use X3P0\Breadcrumbs\Support\Pagination;

/**
 * Renders the trail as a plain, semantic ordered list wrapped in a `<nav>`
 * element, with no structured-data vocabulary. Serves as the base for the
 * Microdata and RDFa formats, which extend it to add their own annotations.
 */
class Html extends Markup implements MarkupBlockOption
{
	/**
	 * Stores the crumb collection, config, and pagination inherited from
	 * `Markup`, plus the resolver used to turn an icon attribute value into
	 * real markup.
	 */
	public function __construct(
		CrumbCollection $crumbs,
		MarkupConfig $config,
		Pagination $pagination,
		private readonly IconResolver $iconResolver
	) {
		parent::__construct($crumbs, $config, $pagination);
	}

	/**
	 * @inheritDoc
	 */
	public static function key(): string
	{
		return MarkupType::Html->value;
	}

	/**
	 * @inheritDoc
	 */
	public static function label(): string
	{
		return __('Plain HTML', 'x3p0-breadcrumbs');
	}

	/**
	 * @inheritDoc
	 */
	public function render(): string
	{
		if (! $this->isRenderable()) {
			return '';
		}

		return sprintf(
			'<nav %s>%s</nav>',
			$this->containerAttr(),
			$this->renderTrail()
		);
	}

	/**
	 * Renders the ordered list of crumbs.
	 */
	protected function renderTrail(): string
	{
		return sprintf(
			'<ol class="%s">%s</ol>',
			esc_attr($this->scopeClass('trail')),
			$this->renderCrumbs()
		);
	}

	/**
	 * Iterates the collection and concatenates the markup for each crumb into
	 * the list's inner HTML.
	 */
	protected function renderCrumbs(): string
	{
		$html = '';

		foreach ($this->crumbs as $crumb) {
			$html .= $this->renderCrumb($crumb);
		}

		return $html;
	}

	/**
	 * Renders a single crumb as a list item, marking the last one with
	 * `aria-current="page"`. Returns an empty string for crumbs that should not
	 * be rendered.
	 */
	protected function renderCrumb(Crumb $crumb): string
	{
		if (! $this->isCrumbRenderable($crumb)) {
			return '';
		}

		return sprintf(
			'<li class="%s"%s>%s%s</li>',
			esc_attr($this->scopeClass([
				'crumb',
				'crumb--' . $crumb->getSlug()
			])),
			$this->crumbs->isLast() ? ' aria-current="page"' : '',
			$this->renderCrumbContent($crumb),
			$this->renderSeparator()
		);
	}

	/**
	 * Renders the inner content of a crumb: the home icon (for the crumb at
	 * the start of the trail, when one is configured) followed by the
	 * (kses-filtered) label, output as a link when the crumb is linkable and
	 * as a plain span otherwise.
	 */
	protected function renderCrumbContent(Crumb $crumb): string
	{
		return $this->isCrumbLinkable($crumb)
			? $this->renderLinkedCrumbContent($crumb)
			: $this->renderUnlinkedCrumbContent($crumb);
	}

	/**
	 * Renders a linkable crumb's content as an anchor.
	 */
	protected function renderLinkedCrumbContent(Crumb $crumb): string
	{
		return sprintf(
			'<a href="%s" class="%s">%s%s</a>',
			esc_url($crumb->getUrl()),
			esc_attr($this->scopeClass('crumb-content')),
			$this->renderCrumbIcon($crumb),
			$this->renderCrumbLabel($crumb)
		);
	}

	/**
	 * Renders a non-linkable crumb's content as a plain span.
	 */
	protected function renderUnlinkedCrumbContent(Crumb $crumb): string
	{
		return sprintf(
			'<span class="%s">%s%s</span>',
			esc_attr($this->scopeClass('crumb-content')),
			$this->renderCrumbIcon($crumb),
			$this->renderCrumbLabel($crumb)
		);
	}

	/**
	 * Renders a crumb's own icon (see `Crumb::getIcon()`), gated by
	 * `isCrumbIconVisible()`. Whatever occupies the first position — `Home`
	 * on a normal site, `Network` on a multisite subsite — is treated as the
	 * trail's home anchor.
	 */
	protected function renderCrumbIcon(Crumb $crumb): string
	{
		return $this->isCrumbIconVisible() ? $this->renderIcon($crumb->getIcon(), 'crumb-icon') : '';
	}

	/**
	 * Determines whether the crumb currently being rendered shows its icon:
	 * every crumb, only the crumb at the start of the trail, only the crumbs
	 * before the last, or none. Shared with `isCrumbLabelHidden()`, which
	 * needs to know whether a hidden label would leave the crumb with
	 * nothing visible at all.
	 */
	protected function isCrumbIconVisible(): bool
	{
		return match ($this->config->iconVisibility()) {
			IconVisibility::All        => true,
			IconVisibility::AllButLast => ! $this->crumbs->isLast(),
			IconVisibility::First      => $this->crumbs->isFirst(),
			IconVisibility::None       => false
		};
	}

	/**
	 * Renders a crumb's label as a (kses-filtered) span. The label always
	 * renders in the markup — even when `isCrumbLabelHidden()` says to hide
	 * it, it stays accessible to assistive tech via a visually-hidden
	 * modifier class rather than being omitted.
	 */
	protected function renderCrumbLabel(Crumb $crumb): string
	{
		$classes = ['crumb-label'];

		if ($this->isCrumbLabelHidden()) {
			$classes[] = 'crumb-label--hidden';
		}

		return sprintf(
			'<span class="%s">%s</span>',
			esc_attr($this->scopeClass($classes)),
			wp_kses($crumb->getLabel(), self::ALLOWED_HTML)
		);
	}

	/**
	 * Determines whether the crumb currently being rendered should hide its
	 * label: every crumb but the first, only the last, or none — per the
	 * config's label visibility. A label is only ever actually hidden when
	 * the same crumb's icon is visible (`isCrumbIconVisible()`); otherwise
	 * the crumb would have nothing visible or accessible standing in for it,
	 * so the label is forced to show regardless of the configured setting.
	 */
	protected function isCrumbLabelHidden(): bool
	{
		$hide = match ($this->config->labelVisibility()) {
			LabelVisibility::All         => false,
			LabelVisibility::AllButFirst => $this->crumbs->isFirst(),
			LabelVisibility::Last        => ! $this->crumbs->isLast(),
			LabelVisibility::None        => true
		};

		return $hide && $this->isCrumbIconVisible();
	}

	/**
	 * Determines whether the separator is rendered after this crumb: between
	 * every crumb, and after the last one too when the config opts in via
	 * `showTrailingSeparator()`.
	 */
	protected function shouldRenderSeparator(): bool
	{
		return ! $this->crumbs->isLast() || $this->config->showTrailingSeparator();
	}

	/**
	 * Renders the configured separator icon, or an empty string when the
	 * separator is turned off, should not be rendered for this crumb, or
	 * none is configured.
	 */
	protected function renderSeparator(): string
	{
		if (! $this->config->showSeparator() || ! $this->shouldRenderSeparator()) {
			return '';
		}

		return $this->renderIcon($this->config->getSeparatorIcon(), 'crumb-separator');
	}

	/**
	 * Resolves an icon attribute value to real markup via {@see IconResolver}
	 * and wraps it in an `aria-hidden` span scoped with the given BEM class.
	 * Returns an empty string when the value is empty or does not resolve to
	 * an icon, so there is nothing to render. Shared by the home and
	 * separator icons, both of which are decorative.
	 */
	protected function renderIcon(string $value, string $class): string
	{
		$html = $this->iconResolver->resolve($value);

		if ('' === $html) {
			return '';
		}

		return sprintf(
			'<span class="%s" aria-hidden="true">%s</span>',
			esc_attr($this->scopeClass($class)),
			$html
		);
	}
}
