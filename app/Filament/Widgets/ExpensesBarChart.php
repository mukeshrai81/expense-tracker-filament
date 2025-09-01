<?php

namespace App\Filament\Widgets;

use App\Enums\DateRange;
use App\Models\Expense;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

use function PHPUnit\Framework\matches;

class ExpensesBarChart extends ChartWidget
{
    protected ?string $heading = 'Expenses BarChart';
    protected ?string $pollingInterval = '5s';    // protected static string $color = 'secondary';
    protected ?string $maxHeight = '300px';
    protected bool $isCollapsible = true;
    public ?string $filter = DateRange::FORTNIGHT->value;

    // Option A: property
    // protected int|string|array $columnSpan = 1; // or 'full', or ['md' => 2, 'xl' => 3]

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
                    'borderColor' => 'rgb(255, 205, 86)',
                    'borderWidth' => 1,
                    'hoverBackgroundColor' => 'rgba(197, 136, 3, 0.2)',
                    'hoverBorderColor' => 'rgba(255, 229, 163, 1)',
                    'borderSkipped' => false,
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
     ** Option B: getter
     ** Control size of the widget
     *! 1–12 → span columns directly.
     *! 'full' → take full width.
     *? 'half' → half width.
     *? 'third' → one-third width.
     */
    public function getColumnSpan(): int | string | array
    {
        return ['default' => 1, 'md' => 2, 'xl' => 3];
    }

    public function getDescription(): ?string
    {
        return 'Expenses Data of ' . $this->filter . ' represented in Bar-Chart';
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
