<?php

namespace App\Areas\Admin\Dashboard\Domain\Entities;

use Spatie\LaravelData\Data;

class DashboardStats extends Data
{
    public function __construct(
        public int $users_total_count,
        public int $users_total_count_unverified,
        public int $users_month_count,
        public int $users_month_count_unverified,
        public int $users_last_month_count,
        public int $users_last_month_count_unverified,
        public int $comic_titles_count,
        public int $comic_series_count,
        public int $comic_issues_count,
        public int $comic_issues_without_series_count,
        public int $comic_publications_count,
    ) {}
}
