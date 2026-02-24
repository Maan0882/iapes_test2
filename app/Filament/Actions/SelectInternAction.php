<?php

namespace App\Filament\Actions;

use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\InternSelectedMail;
use App\Models\InterviewEvaluation;
use App\Models\InterviewBatch;

class SelectInternAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Select')
            ->color('success')
            ->icon('heroicon-o-check-circle')
            ->action(function ($record) {

                $application = $record->application;
                $batch = $application->interviewBatches()->first();

                if (! $batch) {
                    Notification::make()
                        ->title('No batch assigned')
                        ->danger()
                        ->send();
                    return;
                }

                $selectedCount = InterviewEvaluation::where('interview_batch_id', $batch->id)
                    ->where('is_selected', true)
                    ->count();

                if ($selectedCount >= $batch->batch_size) {
                    Notification::make()
                        ->title('Batch limit reached')
                        ->danger()
                        ->send();
                    return;
                }

                $record->update([
                    'is_selected' => true,
                ]);

                Mail::to($application->email)
                    ->send(new InternSelectedMail($application, 'selected'));

                Notification::make()
                    ->title('Intern selected!')
                    ->success()
                    ->send();
            });
    }
}
