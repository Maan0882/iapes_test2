<?php

namespace App\Filament\Resources\InterviewBatchResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Support\Enums\Alignment;


class ApplicationsRelationManager extends RelationManager
{
    protected static string $relationship = 'applications';

    protected static ?string $title = 'Interns';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->poll('3s') // Auto-refresh every 3 seconds to get attendance updates
            
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email'),

                TextColumn::make('domain')
                    ->badge(),

                TextColumn::make('status')
                    ->badge(),

                // ✅ Attendance from pivot table
                IconColumn::make('pivot.is_present')
                    ->label('Attendance')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->alignment(Alignment::Center),
            ])
            // ->recordClasses(function ($record) {
            //     return match ($record->pivot->attendance_status) {
            //         'present' => 'bg-green-50 dark:bg-green-900/20',
            //         'absent' => 'bg-red-50 dark:bg-red-900/20',
            //         default => null,
            //     };
            // })
            ->filters([
                Tables\Filters\SelectFilter::make('attendance_status')
                    ->label('Attendance')
                    ->options([
                        'present' => 'Present',
                        'absent' => 'Absent',
                        'not_marked' => 'Not Marked',
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['value'] === 'not_marked') {
                            $query->whereNull('interview_batch_application.attendance_status');
                        } else {
                            $query->wherePivot('attendance_status', $data['value']);
                        }
                    }),
            ])
            ->headerActions([]) // ❌ Prevent attaching new interns

            ->actions([
                Tables\Actions\Action::make('toggleAttendance')
                    ->label(fn ($record) =>
                        $record->pivot->is_present
                            ? 'Mark Absent'
                            : 'Mark Present'
                    )
                    ->icon(fn ($record) =>
                        $record->pivot->is_present
                            ? 'heroicon-o-x-circle'
                            : 'heroicon-o-check-circle'
                    )
                    ->color(fn ($record) =>
                        $record->pivot->is_present
                            ? 'danger'
                            : 'success'
                    )
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $batch = $this->getOwnerRecord();
                        $record->pivot->update([
                            'is_present' => ! $record->pivot->is_present,
                        ]);
                        //$livewire->dispatch('$refresh');
                    }),
            ])

            ->bulkActions([]); // ❌ No bulk delete
    }
}
