<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InterviewEvaluationResource\Pages;
use App\Filament\Resources\InterviewEvaluationResource\RelationManagers;
use App\Models\InterviewEvaluation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\{Section, TextInput, Textarea, Select};
use Filament\Tables\Actions\{Action, BulkAction, BulkActionGroup};
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Collection;
use Filament\Support\Enums\Alignment;
use App\Models\Application;
use Illuminate\Support\Facades\Mail;
use App\Mail\InternSelectedMail;


class InterviewEvaluationResource extends Resource
{
    protected static ?string $model = InterviewEvaluation::class;

    protected static ?string $navigationGroup = 'Interview Management';
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Section::make('Intern Details')
                //     ->schema([
                        Select::make('application_id')
                            ->label('Select Intern')
                            ->options(
                                Application::where('status', 'interview_scheduled')
                                    ->whereDoesntHave('evaluation')
                                    ->pluck('name', 'id')
                            )
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                $batch = $record->interviewBatches->first();

                                return $record->name
                                    . ' (ID: ' . $record->id . ')'
                                    . ' | Batch: ' . ($batch->batch_name ?? 'N/A')
                                    . ' | Date: ' . ($batch->interview_date ?? 'N/A')
                                    . ' | Time: ' . ($batch->interview_time ?? 'N/A');
                            })
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {

                                $application = \App\Models\Application::with('interviewBatches')
                                    ->find($state);

                                if ($application) {

                                    $batch = $application->interviewBatches->first();

                                    if ($batch) {
                                        $set('batch_name', $batch->batch_name);
                                        $set('interview_date', $batch->interview_date->format('d-m-Y'));
                                        $set('interview_time', $batch->start_time);
                                    }
                                }
                            })
                            ->required(),

                        TextInput::make('batch_name')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('interview_date')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('interview_time')
                            ->disabled()
                            ->dehydrated(false),

                Section::make('Interview Scores')
                    ->schema([
                        
                        TextInput::make('problem_solving')
                            ->label('Problem Solving (Max 25)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(25)
                            ->rule('integer')
                            ->rule('between:0,25')
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $get, callable $set) =>
                                $set('total',
                                    (int) $get('problem_solving')
                                    + (int) $get('aptitude')
                                )
                            ),

                        TextInput::make('aptitude')
                            ->label('Aptitude (Max 25)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(25)
                            ->rule('integer')
                            ->rule('between:0,25')
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $get, callable $set) =>
                                $set('total',
                                    (int) $get('problem_solving')
                                    + (int) $get('aptitude')
                                )
                            ),

                        TextInput::make('total')
                            ->label('Total Score')
                            ->disabled()
                            ->dehydrated()
                            ->reactive(),

                        Textarea::make('remarks')

                    ])->columns(2)
                    //->alignment(Alignment::Center),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table

            ->columns([
                Tables\Columns\TextColumn::make('application.name')
                    ->label('Intern Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('application.interviewBatches.batch_name')
                    ->label('Batch Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('application.interviewBatches.interview_date')
                    ->label('Interview Date')
                    ->date('d-m-Y')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('application.interviewBatches.interview_time')
                    ->label('Interview Time'),

                Tables\Columns\TextColumn::make('problem_solving')
                    ->label('Problem Solving')
                    ->sortable(),

                Tables\Columns\TextColumn::make('aptitude')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total Score')
                    ->formatStateUsing(fn ($state) => $state . ' / 50')
                    ->badge()
                    ->color(fn ($state) =>
                        $state >= 40 ? 'success' :
                        ($state >= 30 ? 'warning' : 'danger')
                    )
                    ->sortable(),

                Tables\Columns\TextColumn::make('rank')
                    ->label('Rank')
                    ->getStateUsing(function ($record) {
                        $ranked = \App\Models\InterviewEvaluation::getRankedByBatch(
                            $record->interview_batch_id
                        );

                        return optional(
                            $ranked->firstWhere('id', $record->id)
                        )->rank;
                    }),

                Tables\Columns\TextColumn::make('ai_suggestion')
                    ->label('AI Suggestion')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Highly Recommended' => 'success',
                        'Recommended' => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\IconColumn::make('is_selected')
                    ->label('Selected')
                    ->boolean(),
                Tables\Columns\TextColumn::make('remarks')
                    ->limit(30),
            ])
            ->defaultSort('total', 'desc')

            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                \App\Filament\Actions\SelectInternAction::make('select'),
                \App\Filament\Actions\RejectInternAction::make('reject'),

            ])
            ->bulkActions([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('bulkSelect')
                        ->label('Bulk Select')
                        ->icon('heroicon-o-check')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {

                            foreach ($records as $record) {

                                $batchId = $record->interview_batch_id;

                                if (!$batchId) {
                                    \Filament\Notifications\Notification::make()
                                        ->title('No batch assigned to evaluation ID: ' . $record->id)
                                        ->danger()
                                        ->send();

                                    continue;
                                }
                                $batch = \App\Models\InterviewBatch::find($batchId);

                                if (! $batch) {
                                    \Filament\Notifications\Notification::make()
                                        ->title('Batch Not Found. This evaluation has no batch assigned.')
                                        ->danger()
                                        ->send();

                                    return;
                                }
                                $selectedCount = \App\Models\InterviewEvaluation::where('interview_batch_id', $batchId)
                                    ->where('is_selected', true)
                                    ->count();

                                if ($selectedCount >= $batch->batch_size) {
                                    continue; // Skip if limit reached
                                }

                                $record->update([
                                    'is_selected' => true,
                                ]);

                                $record->application->update([
                                    'status' => 'selected'
                                ]);

                                \Mail::to($record->application->email)
                                    ->queue(new \App\Mail\InternSelectedMail($record->application));
                            }
                        }),

            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInterviewEvaluations::route('/'),
            'create' => Pages\CreateInterviewEvaluation::route('/create'),
            'edit' => Pages\EditInterviewEvaluation::route('/{record}/edit'),
        ];
    }
}
