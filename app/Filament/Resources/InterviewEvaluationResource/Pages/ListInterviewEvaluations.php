<?php

namespace App\Filament\Resources\InterviewEvaluationResource\Pages;

use App\Filament\Resources\InterviewEvaluationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInterviewEvaluations extends ListRecords
{
    protected static string $resource = InterviewEvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
