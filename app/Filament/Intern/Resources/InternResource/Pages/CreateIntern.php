<?php

namespace App\Filament\Intern\Resources\InternResource\Pages;

use App\Filament\Intern\Resources\InternResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Intern;
use App\Models\Application;

class CreateIntern extends CreateRecord
{
    protected static string $resource = InternResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 1. Generate the TS(Year)/WD/(no) ID
        $currentYear = date('Y');
        $internCount = Intern::whereYear('created_at', $currentYear)->count() + 1;
        $data['intern_id'] = 'TS' . $currentYear . '/WD/' . str_pad($internCount, 3, '0', STR_PAD_LEFT);

        return $data;
    }

    protected function afterCreate(): void
    {
        // 2. Retrieve the application_id from the form component state
        // Since we set dehydrated(false) on application_id, it isn't in $this->record, 
        // but we can get it from the raw form data.
        $applicationId = $this->data['application_id'] ?? null;

        if ($applicationId) {
            $application = Application::find($applicationId);
            if ($application) {
                // Update the status to prevent duplicate intern creations
                $application->update([
                    'status' => 'onboarded'
                ]);
            }
        }
    }
}
