<?php

/**
 * Breadcrumbs label enum.
 *
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009-2026, Justin Tadlock
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://github.com/x3p0-dev/x3p0-breadcrumbs
 */

declare(strict_types=1);

namespace X3P0\Breadcrumbs;

/**
 * Keys for the built-in breadcrumb labels. Each case value is the slug used to
 * look a label up in the config, and `text()` supplies the default, translated
 * string. Config overrides keyed by the same slug take precedence, so this enum
 * is the single source of the built-in copy. The set is not limited to labels
 * with a matching crumb type (e.g., `Untitled` is a fallback used by crumbs).
 */
enum BreadcrumbsLabel: string
{
	case Home          = 'home';
	case Untitled      = 'untitled';
	case Error404      = 'error_404';
	case Archives      = 'archives';
	case Search        = 'search';
	case Paged         = 'paged';
	case PagedComments = 'paged_comments';
	case ArchiveHour   = 'archive_hour';
	case ArchiveMinute = 'archive_minute';
	case ArchiveSecond = 'archive_second';
	case ArchiveWeek   = 'archive_week';
	case ArchiveDay    = 'archive_day';
	case ArchiveMonth  = 'archive_month';
	case ArchiveYear   = 'archive_year';

	/**
	 * Returns the default, translated label text for the case. Values with
	 * a `%s` placeholder are filled in by the crumb that consumes them
	 * (e.g., a page number or a date/time format).
	 */
	public function text(): string
	{
		// phpcs:ignore PHPCompatibility.Variables.ForbiddenThisUseContexts.OutsideObjectContext
		return match ($this) {
			self::Home          => __('Home', 'x3p0-breadcrumbs'),
			self::Untitled      => __('Untitled', 'x3p0-breadcrumbs'),
			self::Error404      => __('Page not found', 'x3p0-breadcrumbs'),
			self::Archives      => __('Archives', 'x3p0-breadcrumbs'),
			// Translators: %s is the search query.
			self::Search        => __('Search results for: %s', 'x3p0-breadcrumbs'),
			// Translators: %s is the page number.
			self::Paged         => __('Page %s', 'x3p0-breadcrumbs'),
			// Translators: %s is the page number.
			self::PagedComments => __('Comment Page %s', 'x3p0-breadcrumbs'),
			// Translators: Hour archive title. %s is the hour time format.
			self::ArchiveHour   => __('Hour %s', 'x3p0-breadcrumbs'),
			// Translators: Minute archive title. %s is the minute time format.
			self::ArchiveMinute => __('Minute %s', 'x3p0-breadcrumbs'),
			// Translators: Second archive title. %s is the second time format.
			self::ArchiveSecond => __('Second %s', 'x3p0-breadcrumbs'),
			// Translators: Weekly archive title. %s is the week date format.
			self::ArchiveWeek   => __('Week %s', 'x3p0-breadcrumbs'),

			// "%s" is replaced with the translated date/time format.
			self::ArchiveDay,
			self::ArchiveMonth,
			self::ArchiveYear   => '%s'
		};
	}
}
