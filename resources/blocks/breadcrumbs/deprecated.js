/**
 * Block deprecations.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

/**
 * Maps the built-in `homeIcon`/`separatorIcon` values used before icons were
 * registered with WordPress's icon API to their `{collection}/{name}`
 * registered-icon equivalent. Mirrors
 * `X3P0\Breadcrumbs\Icon\IconResolver::DEPRECATED_ICONS` on the
 * PHP side, which performs the same remap for content saved before this
 * migration.
 */
const DEPRECATED_ICON_MAP = {
	'svg-arrow':          'x3p0-breadcrumbs/arrow',
	'svg-chevron':        'x3p0-breadcrumbs/chevron',
	'svg-chevron-double': 'x3p0-breadcrumbs/chevron-double',
	'svg-triangle':       'x3p0-breadcrumbs/triangle',
	'text-🏠':            'x3p0-breadcrumbs/emoji-house',
	'text-🏡':            'x3p0-breadcrumbs/emoji-house-garden',
	'text-🏘':            'x3p0-breadcrumbs/emoji-houses',
	'svg-outline':        'x3p0-breadcrumbs/home-outline',
	'svg-fill':           'x3p0-breadcrumbs/home-fill',
	'svg-house-outline':  'x3p0-breadcrumbs/house-outline',
	'svg-house-fill':     'x3p0-breadcrumbs/house-fill'
};

/**
 * Resolves a `homeIcon`/`separatorIcon` value to its registered-icon
 * equivalent when it's one of the deprecated built-in keys, otherwise
 * returns the value unchanged.
 * @param {string} value
 * @returns {string}
 */
const mapDeprecatedIcon = (value) => DEPRECATED_ICON_MAP[value] ?? value;

/**
 * Content saved before 5.0.0 (i.e., up through 4.1.0). Its `homeIcon`/
 * `separatorIcon` values may still use the built-in `svg-chevron`-style keys
 * that predate WordPress's icon registration API, and it may still carry the
 * `showHomeLabel` boolean that predates the `labelVisibility` attribute.
 * Mirrors `X3P0\Breadcrumbs\Block\Renderer\Breadcrumbs::mapDeprecatedAttributes()`
 * on the PHP side, which performs the same migrations for content rendered
 * without ever passing through the editor.
 */
const v5_0_0 = {
	"attributes" : {
		"justifyContent": {
			"type": "string",
			"default": ""
		},
		"showHomeLabel": {
			"type": "boolean",
			"default": true
		},
		"showOnHomepage": {
			"type": "boolean",
			"default": false
		},
		"showTrailStart": {
			"type": "boolean",
			"default": true
		},
		"showTrailEnd": {
			"type": "boolean",
			"default": true
		},
		"homeIcon": {
			"type": "string",
			"default": ""
		},
		"labels": {
			"type": "object",
			"default": {},
			"role": "content"
		},
		"linkTrailEnd": {
			"type": "boolean",
			"default": false
		},
		"mapRewriteTags": {
			"type": "object",
			"default": {
				"post": true
			}
		},
		"markup": {
			"type": "string",
			"default": "rdfa"
		},
		"postTaxonomy": {
			"type": "object",
			"default": {}
		},
		"showTrailingSeparator": {
			"type": "boolean",
			"default": false
		},
		"separatorIcon": {
			"type": "string",
			"default": "svg-chevron"
		},
		"separatorColor": {
			"type": "string"
		},
		"customSeparatorColor": {
			"type": "string"
		}
	},
	isEligible(attributes) {
		return (
			DEPRECATED_ICON_MAP.hasOwnProperty(attributes.homeIcon) ||
			DEPRECATED_ICON_MAP.hasOwnProperty(attributes.separatorIcon) ||
			attributes.hasOwnProperty('showHomeLabel')
		);
	},
	migrate(attributes) {
		const { showHomeLabel, ...otherAttributes } = attributes;

		return {
			...otherAttributes,
			...(attributes.homeIcon && {
				homeIcon: mapDeprecatedIcon(attributes.homeIcon)
			}),
			...(attributes.separatorIcon && {
				separatorIcon: mapDeprecatedIcon(attributes.separatorIcon)
			}),
			...(! showHomeLabel && { labelVisibility: 'all-but-first' })
		};
	}
};

/**
 * Content saved before 4.0.0, when the icon-related attributes were merged
 * into single attributes: `separator`/`separatorType` → `separatorIcon`,
 * `homePrefix`/`homePrefixType` → `homeIcon`.
 */
const v4_0_0 = {
	"attributes" : {
		"justifyContent": {
			"type": "string",
			"default": ""
		},
		"showHomeLabel": {
			"type": "boolean",
			"default": true
		},
		"showOnHomepage": {
			"type": "boolean",
			"default": false
		},
		"showTrailStart": {
			"type": "boolean",
			"default": true
		},
		"showTrailEnd": {
			"type": "boolean",
			"default": true
		},
		"homePrefix": {
			"type": "string",
			"default": ""
		},
		"homePrefixType": {
			"type": "string",
			"default": ""
		},
		"markup": {
			"type": "string",
			"default": "rdfa"
		},
		"separator": {
			"type": "string",
			"default": "chevron"
		},
		"separatorType": {
			"type": "string",
			"default": "mask"
		}
	},
	isEligible(attributes) {
		return (
			attributes.hasOwnProperty('separator') ||
			attributes.hasOwnProperty('separatorType') ||
			attributes.hasOwnProperty('homePrefix') ||
			attributes.hasOwnProperty('homePrefixType')
		);
	},
	migrate(attributes) {
		const {
			separator,
			separatorType,
			homePrefix,
			homePrefixType,
			...otherAttributes
		} = attributes;

		// This version predates `DEPRECATED_ICON_MAP`'s `{type}-{icon}` key
		// format, storing the same information as separate attributes
		// instead. Rebuild that key here so it can be mapped the same way
		// as the deprecation above. `mask` was this version's name for
		// what's now the `svg` type, so it's normalized before mapping;
		// an unset type defaults to `svg` and an unset icon to `chevron`,
		// matching this version's own defaults.
		let separatorIcon;
		if (separator || separatorType) {
			const type = 'mask' === separatorType ? 'svg' : (separatorType || 'svg');
			const icon = separator || 'chevron';
			separatorIcon = mapDeprecatedIcon(`${type}-${icon}`);
		}

		// Home icons had no default, so both parts must be present.
		let homeIcon;
		if (homePrefix && homePrefixType) {
			const type = 'mask' === homePrefixType ? 'svg' : homePrefixType;
			homeIcon = mapDeprecatedIcon(`${type}-${homePrefix}`);
		}

		return {
			...otherAttributes,
			...(separatorIcon && { separatorIcon }),
			...(homeIcon && { homeIcon }),
		};
	}
};

/**
 * Returns an array for use in block deprecations.
 */
export default [v5_0_0, v4_0_0];
