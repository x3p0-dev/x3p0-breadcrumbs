/**
 * Icon grid component.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

// WordPress dependencies.
import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';
import { safeHTML } from '@wordpress/dom';

/**
 * Renders a grid of selectable icons pulled from the WordPress icon
 * library (`root/icon` REST entities), each identified by its `content`:
 * raw, sanitized SVG markup returned by the REST API rather than JSX, so it
 * is injected via `dangerouslySetInnerHTML`. An icon may instead carry a
 * plain `text` glyph (e.g. the separator's text/punctuation options, which
 * aren't registrable SVG icons) and is rendered directly instead.
 * @param props
 * @returns {JSX.Element}
 */
export const IconGrid = ({
	icons,
	value,
	onSelect
}) => (
	! icons.length ? (
		<div className="x3p0-breadcrumbs-icon-grid__no-results">
			<p>{__('No icons found.', 'x3p0-breadcrumbs')}</p>
		</div>
	) : (
		<div className="x3p0-breadcrumbs-icon-grid">
			{icons.map((icon) => (
				<Button
					key={icon.name}
					className="x3p0-breadcrumbs-icon-grid__item"
					label={icon.label}
					showTooltip
					isPressed={icon.name === value}
					onClick={() => onSelect(icon.name)}
				>
					{icon.content ? (
						<span
							className="x3p0-breadcrumbs-icon-grid__item-svg"
							dangerouslySetInnerHTML={{__html: safeHTML(icon.content)}}
						/>
					) : (
						<span className="x3p0-breadcrumbs-icon-grid__item-text">
							{icon.text}
						</span>
					)}
					<span className="x3p0-breadcrumbs-icon-grid__item-label">
						{icon.label}
					</span>
				</Button>
			))}
		</div>
	)
);
