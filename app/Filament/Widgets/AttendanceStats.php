<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
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
                DB::table('interview_batch_intern')
                    ->where('is_present', 1)
                    ->count()
            )->color('success'),

            Stat::make('Absent',
                DB::table('interview_batch_intern')
                    ->where('is_present', 0)
                    ->count()
            )->color('danger'),
        ];
    }
}
