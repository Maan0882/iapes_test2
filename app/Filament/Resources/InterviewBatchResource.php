<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InterviewBatchResource\Pages;
use App\Filament\Resources\InterviewBatchResource\RelationManagers;
use App\Models\InterviewBatch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Collection;

class InterviewBatchResource extends Resource
{
    protected static ?string $model = InterviewBatch::class;

    protected static ?string $navigationGroup = 'Interview Management';
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Interview Details')
                    ->schema([
                        TextInput::make('batch_name')
                            ->required()
                            ->placeholder('Batch A / Batch 1'),

                        DatePicker::make('interview_date')
                            ->required(),

                        TimePicker::make('start_time')
                            ->required(),

                        TimePicker::make('end_time')
                            ->required(),

                        TextInput::make('batch_size')
                            ->numeric()
                            ->default(5)
                            ->required(),

                        TextInput::make('location')
                            ->label('Interview Location (Offline)')
                            ->required(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('batch_name')->searchable()->sortable(),
                TextColumn::make('interview_date')->date(),
                TextColumn::make('start_time'),
                TextColumn::make('end_time'),
                TextColumn::make('batch_size'),
                TextColumn::make('applications_count')
                    ->counts('applications')
                    ->label('Interns Assigned'),
                Tables\Columns\BadgeColumn::make('status')
                ->colors([
                    'success' => 'open',
                    'warning' => 'full',
                    'primary' => 'completed',
                    'danger' => 'canceled',
                ])
            ])
            ->defaultSort('interview_date')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                //Tables\Actions\EditAction::make(),
                Action::make('download_pdf')
                    ->label('Download Attendance')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function ($record) {

                        $pdf = Pdf::loadView('pdf.batch_attendance', [
                            'batch' => $record,
                            'applications' => $record->applications,
                        ]);

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            "Batch_{$record->batch_name}_Attendance.pdf"
                        );
                    }),
                Action::make('Download Report')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url('/admin/interview-report')
                    ->openUrlInNewTab()
                    ->visible(fn () =>
                        \App\Models\Application::where('status', 'interview_completed')->count()
                        ===
                        \App\Models\Application::count()
                    ),

                Action::make('cancelBatch')
                    ->label('Cancel Batch')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status !== 'completed')
                    ->requiresConfirmation()
                    ->modalHeading('Cancel Interview Batch')
                    ->modalDescription('Select what should happen to interns in this batch.')
                    ->form([
                        Forms\Components\Select::make('application_status')
                            ->label('Update Intern Status To')
                            ->options([
                                'applied' => 'Move back to Applied',
                                'canceled' => 'Mark Interns as Canceled',
                                'rescheduled' => 'Mark as Rescheduled',
                            ])
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {

                        // 1️⃣ Update batch status
                        $record->status = 'canceled';
                        $record->save();

                        // 2️⃣ Update related applications
                        foreach ($record->applications as $application) {
                            $application->status = $data['application_status'];
                            $application->save();
                        }
                    }),
                Action::make('completeBatch')
                    ->label('Mark as Completed')
                    ->icon('heroicon-o-check-circle')
                    ->color('primary')
                    ->visible(fn ($record) => $record->status !== 'completed')
                    ->requiresConfirmation()
                    ->action(function ($record) {

                        $record->status = 'completed';
                        $record->save();

                        foreach ($record->applications as $application) {
                            $application->status = 'interview_completed';
                            $application->save();
                        }

                    }),

            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                // Bulk action for multiple batches (applies to all interns in selected batches)
                BulkAction::make('updateInternsStatusBulk')
                    ->label('Update Interns Status')
                    ->action(function (Collection $records, array $data) {
                        foreach ($records as $batch) {
                            foreach ($batch->applications as $application) {
                                $application->status = $data['status'];
                                $application->save();
                            }
                        }
                    })
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Select New Status')
                            ->options([
                                'Applied' => 'Applied',
                                'Interview Scheduled' => 'Interview Scheduled',
                                'Canceled' => 'Canceled',
                                'Rescheduled' => 'Rescheduled',
                            ])
                            ->required(),
                    ])
                    ->requiresConfirmation(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ApplicationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
        'index' => Pages\ListInterviewBatches::route('/'),
        'create' => Pages\CreateInterviewBatch::route('/create'),
        'view' => Pages\ViewInterviewBatch::route('/{record}'),
        'edit' => Pages\EditInterviewBatch::route('/{record}/edit'),
        ];
    }
}
