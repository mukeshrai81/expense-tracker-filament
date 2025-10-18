<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\CategoryResource\Pages\ListCategories;
use App\Filament\Resources\CategoryResource\Pages\CreateCategory;
use App\Filament\Resources\CategoryResource\Pages\EditCategory;
use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static string | \UnitEnum | null $navigationGroup = 'Finance';
    protected static ?string $navigationLabel = 'Cats';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->label('Category Name')
                    ->placeholder('Enter category name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),

                // 👉 Custom column: Expense Count
                TextColumn::make('expenses_count')
                    ->label('#Expense')
                    // ->counts('expenses') // auto-counts the relationship
                    ->badge()
                    ->colors([
                        'warning' => fn($state) => $state == 0,
                        'success' => fn($state) => ($state > 0 && $state <= 5),
                        'info' => fn($state) => ($state > 5 && $state <= 10),
                        'grey' => fn($state) => ($state > 10 && $state <= 20),
                        'danger' => fn($state) => $state > 20
                    ])
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('expenses_sum_amount')
                    ->label('∑Expense')
                    ->sum('expenses', 'amount')
                    ->badge()
                    ->colors([
                        'warning' => fn($state) => $state == 0,
                        'success' => fn($state) => ($state > 0 && $state <= 500),
                        'info' => fn($state) => ($state > 500 && $state <= 2000),
                        'grey' => fn($state) => ($state > 2000 && $state <= 5000),
                        'danger' => fn($state) => $state > 5000
                    ])
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')->dateTime('d M Y h:ia')->label('Created')->sortable(),
            ])
            ->defaultSort('name', 'ASC')
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
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
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('expenses'); // Adds `expenses_count` field
    }
}
