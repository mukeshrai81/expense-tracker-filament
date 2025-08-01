<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        // Always redirect to the index page after creation
        return static::getResource()::getUrl('index');
    }
}
