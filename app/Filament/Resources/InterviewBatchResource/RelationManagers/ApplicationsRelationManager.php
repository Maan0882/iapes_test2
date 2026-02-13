<?php

namespace App\Filament\Resources\InterviewBatchResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class ApplicationsRelationManager extends RelationManager
{
    protected static string $relationship = 'applications';

    protected static ?string $title = 'Interns';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')

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
                    ->falseColor('warning'),
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
                        $record->pivot->update([
                            'is_present' => ! $record->pivot->is_present,
                        ]);
                        $livewire->dispatch('$refresh');
                    }),
            ])

            ->bulkActions([]); // ❌ No bulk delete
    }
}
