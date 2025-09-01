<?php

namespace App\Filament\Widgets;

use App\Enums\DateRange;
use App\Models\Category;
use App\Models\Expense;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class ExpensesPieChart extends ChartWidget
{
    protected ?string $heading = 'Expenses by Category';
    protected ?string $pollingInterval = '5s';    // protected static string $color = 'secondary';
    protected string $color = 'info';
    protected ?string $maxHeight = '300px';
    protected bool $isCollapsible = true;
    public ?string $filter = DateRange::FORTNIGHT->value;

    /**
     ** Types =  bar, line, pie, doughnut, radar, polarArea, mixed type
     */
    protected function getType(): string
    {
        return 'doughnut';
    }

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

        $data = Expense::query()
            ->selectRaw('SUM(amount) as total_amount, categories.name as category_name')
            ->join('categories', 'expenses.category_id', '=', 'categories.id')
            ->whereBetween('expenses.date', [$startDate, now()]) // Filter by the date range
            ->groupBy('category_name')
            ->get();

        // Separate the data and labels from the collection
        $labels = $data->pluck('category_name')->toArray();
        $totals = $data->pluck('total_amount')->toArray();

        return [
            'datasets' => [
                [
                    'label' => "Expenses Data",
                    'data' => $totals,
                    // 'backgroundColor' => 'rgba(255, 205, 86, 0.2)',
                    'backgroundColor' => [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#183aacff',
                        '#46bb02ff',
                    ],
                    'borderColor' => 'rgb(255, 205, 86)',
                    'borderWidth' => 1,
                    'hoverBackgroundColor' => 'rgba(197, 136, 3, 0.2)',
                    'hoverBorderColor' => 'rgba(255, 229, 163, 1)',
                    'borderSkipped' => false,
                    // 'clip' => false
                ]
            ],
            'labels' => $labels,
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
}
