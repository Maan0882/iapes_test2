<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Application;

class AttendanceStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Interns',
                Application::count()
            ),

            Stat::make('Interview Scheduled',
                Application::where('status', 'Interview Scheduled')->count()
            ),

            Stat::make('Present',
                Application::where('attendance', 'Present')->count()
            )->color('success'),

            Stat::make('Absent',
                Application::where('attendance', 'Absent')->count()
            )->color('danger'),
        ];
    }
}
