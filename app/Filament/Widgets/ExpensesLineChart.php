<?php

namespace App\Filament\Widgets;

use App\Enums\DateRange;
use App\Models\Expense;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class ExpensesLineChart extends ChartWidget
{
    protected ?string $heading = 'Expenses Line Chart';
    protected ?string $pollingInterval = '5s';    // protected static string $color = 'secondary';
    protected ?string $maxHeight = '300px';
    protected bool $isCollapsible = true;
    public ?string $filter = DateRange::FORTNIGHT->value;

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        $startDate = match ($activeFilter) {
            DateRange::WEEK->value    => now()->startOfWeek(),
            DateRange::FORTNIGHT->value => now()->subDays(15),
            DateRange::MONTH->value => now()->startOfMonth(),
            DateRange::YEAR->value => now()->startOfYear(),
            default => now()->subDays(10)
        };

        $data = Trend::model(Expense::class)
            ->dateColumn('date')
            ->between(
                start: $startDate,
                end: now(),
            )
            ->perDay()
            ->sum('amount');

        return [
            'datasets' => [
                [
                    'label' => "Expenses Data",
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => 'rgba(255, 205, 86, 0.2)',
                    'borderColor' => 'rgba(221, 30, 24, 1)',
                    'borderWidth' => 1,
                    'hoverBackgroundColor' => 'rgba(102, 226, 216, 0.2)',
                    'hoverBorderColor' => 'rgba(202, 198, 186, 1)',
                    'borderSkipped' => false,
                    'tension' => 0.5
                    // 'fill' => true
                    // 'clip' => false
                ]
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            "" => 'Default',
            DateRange::WEEK->value => 'This Week',
            DateRange::FORTNIGHT->value => 'This Fortnight',
            DateRange::MONTH->value => 'This Month',
            DateRange::YEAR->value => 'This Year',
        ];
    }

    /**
     ** Types =  bar, line, pie, doughnut, radar, polarArea, mixed type
     */
    protected function getType(): string
    {
        return 'line';
    }
}
