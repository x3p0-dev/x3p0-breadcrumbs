/**
 * Block deprecations.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2025, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

export default [
	{
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

			let separatorIcon;
			if (separator || separatorType) {
				const type = 'mask' === separatorType ? 'svg' : (separatorType || 'svg');
				const icon = separator || 'chevron';
				separatorIcon = `${type}-${icon}`;
			}

			let homeIcon;
			if (homePrefix && homePrefixType) {
				const type = 'mask' === homePrefixType ? 'svg' : homePrefixType;
				homeIcon = `${type}-${homePrefix}`;
			}

			return {
				...otherAttributes,
				...(separatorIcon && { separatorIcon }),
				...(homeIcon && { homeIcon }),
			};
		}
	}
];
