<?php

namespace App\Filament\Intern\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class InternWelcomeWidget extends Widget
{
    protected static string $view = 'filament.intern.widgets.intern-welcome-widget';

    // Make the widget span the entire width of the dashboard (2 columns)
    protected int | string | array $columnSpan = 'full';

    // Pass data to the Blade view
    protected function getViewData(): array
    {
        $intern = Auth::guard('intern')->user();

        return [
            'intern' => $intern,
            // You can fetch and pass evaluation remarks here if you stored them, 
            // or fetch assigned tasks/projects from other models.
        ];
    }
}
