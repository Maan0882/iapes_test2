<?php

namespace App\Filament\Actions;

use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\InternRejectedMail;

class RejectInternAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Reject')
            ->color('danger')
            ->icon('heroicon-o-x-circle')
            ->requiresConfirmation()
            ->modalHeading('Reject Intern')
            ->modalDescription('Are you sure you want to reject this intern?')
            ->visible(fn ($record) => ! $record->is_selected && ! $record->is_rejected)
            ->action(function ($record) {

                $record->update([
                    'is_rejected' => true,
                ]);

                $record->application->update([
                    'status' => 'rejected',
                ]);

                Mail::to($record->application->email)
                    ->send(new InternRejectedMail($record->application));

                Notification::make()
                    ->title('Intern rejected')
                    ->success()
                    ->send();
            });
    }
}
