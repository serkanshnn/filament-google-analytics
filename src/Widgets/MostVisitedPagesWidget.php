<?php

namespace BezhanSalleh\FilamentGoogleAnalytics\Widgets;

use BezhanSalleh\FilamentGoogleAnalytics\Traits;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\OrderBy;
use Spatie\Analytics\Period;

class MostVisitedPagesWidget extends ChartWidget
{
    use Traits\CanViewWidget;

    protected static string $view = 'filament-google-analytics::widgets.most-visited-pages';

    protected static ?string $pollingInterval = null;

    protected static ?int $sort = 3;

    public ?string $filter = 'T';

    public function getHeading(): string | Htmlable | null
    {
        return __('filament-google-analytics::widgets.most_visited_pages');
    }

    protected function getFilters(): array
    {
        return [
            'T' => __('filament-google-analytics::widgets.T'),
            'TW' => __('filament-google-analytics::widgets.TW'),
            'TM' => __('filament-google-analytics::widgets.TM'),
            'TY' => __('filament-google-analytics::widgets.TY'),
        ];
    }

    protected function getData(): array
    {
        $lookups = [
            'T' => Period::create(Carbon::today(), Carbon::now()),
            'TW' => Period::create(Carbon::today()->subDays(7), Carbon::now()),
            'TM' => Period::create(Carbon::today()->subMonth(), Carbon::now()),
            'TY' => Period::create(Carbon::today()->subYear(), Carbon::now()),
        ];

        $analyticsData = Analytics::get(
            $lookups[$this->filter],
            ['screenPageViews'],
            ['pageTitle', 'hostName', 'pagePath'],
            10,
            [OrderBy::metric('screenPageViews', true)],
        );
        $headers = [
            'name',
            'hostname',
            'path',
            'visits',
        ];

        return array_map(
            function ($row) use ($headers) {
                return array_combine($headers, $row);
            },
            $analyticsData->toArray() ?? []
        );
    }

    protected function getType(): string
    {
        return 'line';
    }
}
