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


class InterviewBatchResource extends Resource
{
    protected static ?string $model = InterviewBatch::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
