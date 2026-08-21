<?php

/**
 * Time archive crumb base class.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs\Crumb\Type;

use X3P0\Breadcrumbs\Icon\IconOptionKey;

/**
 * Base for the sub-day time archives (Hour, Minute, Second), which WordPress
 * has no permastruct function for. Builds the archive URL from the date
 * permastruct extended with the concrete class's own date/time formats,
 * falling back to a `?m=` query URL when pretty permalinks are off.
 */
abstract class TimeArchive extends Date
{
	/**
	 * Points every sub-day time archive (`Hour`, `Minute`, `Second`) at the
	 * shared `time` icon option, so all of them are configured with one
	 * setting, separately from the broader date archives.
	 *
	 * @inheritDoc
	 */
	public function iconOptionKey(): IconOptionKey
	{
		return IconOptionKey::Time;
	}

	/**
	 * Returns this archive's own permastruct tags in order, keyed by tag
	 * name (`hour`, `minute`, `second`) to the `get_the_time()` date/time
	 * format character used to resolve each. Combined with `year`/
	 * `monthnum`/`day`, which every time archive needs regardless of its
	 * own precision.
	 *
	 * @return array<string, string>
	 */
	abstract protected function formats(): array;

	/**
	 * @inheritDoc
	 */
	public function getUrl(): string
	{
		$formats = [
			'year'     => 'Y',
			'monthnum' => 'm',
			'day'      => 'd',
			...$this->formats()
		];

		$values = [];

		foreach ($formats as $tag => $format) {
			$values[$tag] = 'year' === $tag
				? get_the_time($format, $this->post)
				: zeroise(absint(get_the_time($format, $this->post)), 2);
		}

		// WordPress doesn't have a structure function for sub-day
		// archives, so we're building off the date structure.
		if ($structure = $GLOBALS['wp_rewrite']->get_date_permastruct()) {
			$structure = trailingslashit($structure) . implode(
				'/',
				array_map(fn (string $tag): string => "%{$tag}%", array_keys($this->formats()))
			);

			foreach ($values as $tag => $value) {
				$structure = str_replace("%{$tag}%", $value, $structure);
			}

			return home_url(user_trailingslashit($structure, array_key_last($this->formats())));
		}

		return home_url('?m=' . implode('', $values));
	}
}
