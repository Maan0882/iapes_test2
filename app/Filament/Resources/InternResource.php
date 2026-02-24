<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InternResource\Pages;
use App\Filament\Resources\InternResource\RelationManagers;
use App\Models\Intern;
use App\Models\Application;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InternResource extends Resource
{
    protected static ?string $model = Intern::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Intern Administration';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Candidate Selection')->schema([
                    
                    // Fetch from Selected Applications
                    Forms\Components\Select::make('application_id') // Keeping this name to match your DB column
                        ->label('Select from Selected Candidates')
                        ->options(Application::where('status', 'selected')->pluck('name', 'id'))
                        ->searchable()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $candidate = Application::with('evaluation')->find($state);
                            if ($candidate) {
                                // Auto-fill the intern's name from the Application
                                $set('name', $candidate->name);
                                $set('email', $candidate->email);
                                $set('phone', $candidate->phone);
                                $set('college', $candidate->college);
                                $set('domain', $candidate->domain);
                                $set('degree', $candidate->degree);
                                $set('last_exam_appeared', $candidate->last_exam_appeared);
                                $set('cgpa', $candidate->cgpa);
                                $set('skills', $candidate->skills);

                            }
                        })->columnSpanFull(),

                ]),

                Forms\Components\Section::make('Intern Profile')->schema([
                    Forms\Components\TextInput::make('name')->required(),
                    Forms\Components\TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('phone'),
                    Forms\Components\TextInput::make('college'),
                    Forms\Components\TextInput::make('domain'),
                    Forms\Components\TextInput::make('degree'),
                    Forms\Components\TextInput::make('last_exam_appeared'),
                    Forms\Components\TextInput::make('cgpa'),
                    Forms\Components\TextInput::make('skills'),
                ])->columns(2),

                Forms\Components\Section::make('Interview Performance (Read-Only)')
                    ->description('Data pulled directly from the interview evaluation.')
                    ->schema([
                        Forms\Components\Placeholder::make('aptitude_score')
                            ->label('Aptitude Score')
                            ->content(fn ($record) => $record?->application?->evaluation?->aptitude ?? 'N/A'),
                            
                        Forms\Components\Placeholder::make('problem_solving_score')
                            ->label('Problem Solving Score')
                            ->content(fn ($record) => $record?->application?->evaluation?->problem_solving ?? 'N/A'),

                        Forms\Components\Placeholder::make('total_score')
                            ->label('Total Score')
                            ->content(fn ($record) => $record?->application?->evaluation?->total ?? 'N/A'),
                            
                        Forms\Components\Placeholder::make('remarks')
                            ->label('Interviewer Remarks')
                            ->content(fn ($record) => $record?->application?->evaluation?->remarks ?? 'N/A')
                            ->columnSpanFull(),
                    ])->columns(3)->hiddenOn('create'), // Hide on creation, show on edit/view


                Forms\Components\Section::make('System Credentials')->schema([
                    // The intern_id is hidden; it generates in the Intern Model boot method
                    
                    Forms\Components\TextInput::make('username')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->required(fn (string $context): bool => $context === 'create')
                        ->dehydrated(fn ($state) => filled($state))
                        ->maxLength(255),
                ])->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('intern_id')
                    ->label('Intern ID')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('domain')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('application.evaluation.total')
                    ->label('Interview Score')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('username')
                    ->copyable()
                    ->searchable(),

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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInterns::route('/'),
            'create' => Pages\CreateIntern::route('/create'),
            'edit' => Pages\EditIntern::route('/{record}/edit'),
        ];
    }
}
