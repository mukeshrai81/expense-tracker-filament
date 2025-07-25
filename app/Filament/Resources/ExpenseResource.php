<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Filament\Resources\ExpenseResource\RelationManagers;
use App\Models\Expense;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?string $navigationLabel = 'Expenses';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('amount')
                    ->numeric()
                    ->required(),
                DatePicker::make('date')->default(Carbon::now())->label('Expense Date')
                    ->required(),
                Textarea::make('description'),
                Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                TagsInput::make('tags')
                    ->label('Tags')
                    ->placeholder('Add tags'),

                // CheckboxList::make('tags')
                //     ->options([
                //         'travel' => 'Travel',
                //         'food' => 'Food',
                //         'utilities' => 'Utilities',
                //         'office' => 'Office',
                //         'misc' => 'Miscellaneous',
                //     ]),

                // Select::make('tags')
                //     ->multiple()
                //     ->options([
                //         'travel' => 'Travel',
                //         'food' => 'Food',
                //         'utilities' => 'Utilities',
                //         'office' => 'Office',
                //         'misc' => 'Miscellaneous',
                //         'fin' => 'Finance',
                //     ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('amount')->money('NPR')->sortable()->toggleable()->colors([
                    'danger' => fn($state) => $state > 200,
                    'success' => fn($state) => $state <= 200,
                ]),
                TextColumn::make('date')->date('D M d, Y D')->sortable()->toggleable()->label('Expense Date'),
                TextColumn::make('category.name')->label('Category')->sortable()->toggleable(),
                TextColumn::make('description')->limit(30)->toggleable(),
                TextColumn::make('tags')->badge()->label('Tags')->separator(', ')->color("warning")->toggleable(),
                TextColumn::make('created_at')->dateTime('M d, Y D h:ia')->label('Created')->sortable()->toggleable()
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('category')->multiple()->relationship('category', 'name'),
                Filter::make('today')
                    ->label('Today')
                    ->query(fn(Builder $query) => $query->whereDate('date', today())),
                Filter::make('this week')
                    ->label('This Week')
                    ->query(
                        fn(Builder $query) => $query
                            ->whereDate('date', '>=', now()->startOfWeek(Carbon::SUNDAY))
                            ->whereDate('date', '<=', now()->endOfWeek())
                    ),

                Filter::make('this_month')
                    ->label('This Month')
                    ->query(
                        fn(Builder $query) => $query
                            ->whereMonth('date', now()->month)
                            ->whereYear('date', now()->year)
                    ),

                Filter::make('date')
                    ->form([
                        DatePicker::make('from')->label('From date'),
                        DatePicker::make('until')->label('Until date'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn($q, $date) => $q->whereDate('date', '>=', $date))
                            ->when($data['until'], fn($q, $date) => $q->whereDate('date', '<=', $date));
                    }),

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotificationTitle('Expenses deleted successfully'),
                ])->label('Bulk Actions')->icon('heroicon-o-ellipsis-horizontal'),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('Export CSV')
                        // ->icon('heroicon-o-download')
                        ->action(fn() => redirect()->route('filament.resources.expenses.export')),
                    Tables\Actions\Action::make('Export PDF')
                        // ->icon('heroicon-o-document-download')
                        ->action(fn() => redirect()->route('filament.resources.expenses.export-pdf')),
                    Tables\Actions\Action::make('Export Excel')
                        // ->icon('heroicon-o-document-download')
                        ->action(fn() => redirect()->route('filament.resources.expenses.export-excel')),
                ])->label('Export Options')->icon('heroicon-o-arrow-down-tray'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
