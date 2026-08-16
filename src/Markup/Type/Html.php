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
use X3P0\Breadcrumbs\Crumb\Type\Home;
use X3P0\Breadcrumbs\Icon\IconResolver;
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
	 * Built-in text/glyph icon values mapped to their literal character.
	 * These aren't SVG files and so can't be registered icons; anything
	 * else falls through to {@see IconResolver} to fetch from the
	 * registered icon library.
	 *
	 * @var  array<string, string>
	 * @todo Type hint with PHP 8.3+ requirement.
	 */
	private const TEXT_ICONS = [
		'text-slash'        => '/',
		'text-bar'          => '|',
		'text-middot'       => '·',
		'text-black-circle' => '●',
		'text-white-circle' => '○'
	];

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
			'<nav %s><ol class="%s">%s</ol></nav>',
			$this->containerAttr(),
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
			$this->shouldRenderSeparator() ? $this->renderSeparator() : ''
		);
	}

	/**
	 * Renders the inner content of a crumb: an icon (for the home crumb, when
	 * one is configured) followed by the (kses-filtered) label wrapped in a
	 * span, output as a link when the crumb is linkable and as a plain span
	 * otherwise.
	 */
	private function renderCrumbContent(Crumb $crumb): string
	{
		$icon = $crumb instanceof Home
			? $this->renderIcon($this->config->getHomeIcon(), 'crumb-icon')
			: '';

		// Filter out any unwanted HTML from the label.
		$label = sprintf(
			'<span class="%s">%s</span>',
			esc_attr($this->scopeClass('crumb-label')),
			wp_kses($crumb->getLabel(), self::ALLOWED_HTML)
		);

		// Return the linked content if the crumb has a URL.
		if ($this->isCrumbLinkable($crumb)) {
			return sprintf(
				'<a href="%s" class="%s">%s%s</a>',
				esc_url($crumb->getUrl()),
				esc_attr($this->scopeClass('crumb-content')),
				$icon,
				$label
			);
		}

		// Return an unlinked span if there's no URL.
		return sprintf(
			'<span class="%s">%s%s</span>',
			esc_attr($this->scopeClass('crumb-content')),
			$icon,
			$label
		);
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
	 * Renders the configured separator icon, or an empty string when none is
	 * configured.
	 */
	protected function renderSeparator(): string
	{
		return $this->renderIcon($this->config->getSeparatorIcon(), 'crumb-separator');
	}

	/**
	 * Resolves an icon attribute value to real markup — a built-in text/glyph
	 * character from {@see self::TEXT_ICONS}, or an icon fetched from the
	 * registered icon library via {@see IconResolver} — and wraps it in an
	 * `aria-hidden` span scoped with the given BEM class. Returns an empty
	 * string when the value is empty or does not resolve to an icon, so
	 * there is nothing to render. Shared by the home and separator icons,
	 * both of which are decorative.
	 */
	protected function renderIcon(string $value, string $class): string
	{
		$html = self::TEXT_ICONS[$value] ?? $this->iconResolver->resolve($value);

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
