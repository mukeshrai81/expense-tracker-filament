<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use App\Filament\Resources\ExpenseResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateExpense extends CreateRecord
{
    protected static string $resource = ExpenseResource::class;

    // protected function getCreatedRedirectUrl(): string
    // {
    //     return static::getResource()::getUrl('index');
    // }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Expense created successfully!')
            ->body('Your expense has been added to the records.')
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
