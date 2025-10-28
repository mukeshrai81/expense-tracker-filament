<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    // protected function getCreatedRedirectUrl(): string
    // {
    //     return static::getResource()::getUrl('index');
    // }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('New Category created successfully!')
            ->body('Your expenses category has been Created.')
            ->icon('heroicon-o-check-circle')
            ->iconColor('success')
            ->duration(3000); // 3 seconds
    }

    protected function getRedirectUrl(): string
    {
        // Always redirect to the index page after creation
        return static::getResource()::getUrl('index');
    }
}
