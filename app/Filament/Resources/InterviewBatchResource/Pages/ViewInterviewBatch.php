<?php

namespace App\Filament\Resources\InterviewBatchResource\Pages;

use App\Filament\Resources\InterviewBatchResource;
use Filament\Resources\Pages\ViewRecord; // ✅ Correct class
use Filament\Actions;

class ViewInterviewBatch extends ViewRecord
{
    protected static string $resource = InterviewBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}