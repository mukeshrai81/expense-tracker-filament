<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\ExpenseResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditExpense extends EditRecord
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Expense Updated !')
            ->body('Your expense has been updated.')
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
