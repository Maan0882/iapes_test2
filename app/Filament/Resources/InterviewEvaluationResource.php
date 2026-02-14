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
                Section::make('Intern Details')
                    ->schema([
                        Select::make('application_id')
                            ->label('Select Intern')
                            ->options(
                                \App\Models\Application::where('status', 'interview_scheduled')
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->required(),

                    ]),

                Section::make('Interview Scores')
                    ->schema([

                        TextInput::make('problem_solving')
                            ->numeric()
                            ->maxValue(25)
                            ->required()
                            ->live(),

                        TextInput::make('aptitude')
                            ->numeric()
                            ->maxValue(25)
                            ->required()
                            ->live(),

                        TextInput::make('total')
                            ->disabled()
                            ->dehydrated()
                            ->default(fn ($get) =>
                                (int) $get('problem_solving') + (int) $get('aptitude')
                            ),

                        Textarea::make('remarks')

                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function afterCreate($record): void
    {
        $record->application->update([
            'status' => 'interview_completed'
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
