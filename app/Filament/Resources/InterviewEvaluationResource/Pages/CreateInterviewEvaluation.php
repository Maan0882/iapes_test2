<?php

namespace App\Filament\Resources\InterviewEvaluationResource\Pages;

use App\Filament\Resources\InterviewEvaluationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Application;

class CreateInterviewEvaluation extends CreateRecord
{
    protected static string $resource = InterviewEvaluationResource::class;

    protected function afterCreate(): void
    {
        $application = $this->record->application;

        $application->update([
            'status' => 'evaluation_completed',
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $application = \App\Models\Application::with('interviewBatches')
            ->find($data['application_id']);

        if (! $application || $application->interviewBatches->isEmpty()) {
            throw new \Exception('Intern is not assigned to any batch.');
        }

        $data['interview_batch_id'] =
            $application->interviewBatches->first()->id;

        return $data;
    }
}
