<?php

namespace App\Filament\Intern\Resources;

use App\Filament\Intern\Resources\InternResource\Pages;
use App\Filament\Intern\Resources\InternResource\RelationManagers;
use App\Models\Intern;
use App\Models\Application;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class InternResource extends Resource
{
    protected static ?string $model = Intern::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Candidate Selection')
                    ->description('Select an approved candidate from the interview evaluations.')
                    ->schema([
                        Forms\Components\Select::make('application_id')
                            ->label('Approved Candidates')
                            ->options(function () {
                                // Fetch applications where the related evaluation has a final_decision of 'selected'
                                return Application::whereHas('interviewEvaluation', function (Builder $query) {
                                    $query->where('final_decision', 'selected');
                                })->pluck('candidate_name', 'id');
                            })
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Eager load the evaluation to prevent N+1 issues
                                $application = Application::with('interviewEvaluation')->find($state);
                                
                                if ($application) {
                                    // Auto-fill Intern credentials based on application data
                                    $set('name', $application->candidate_name);
                                    
                                    // Generate a suggested username (e.g., john.doe)
                                    $suggestedUsername = strtolower(str_replace(' ', '.', $application->candidate_name));
                                    $set('username', $suggestedUsername);

                                    // Populate contextual evaluation data for the admin to review
                                    $set('eval_score', $application->interviewEvaluation->total_score ?? 'N/A');
                                    $set('eval_remarks', $application->interviewEvaluation->remarks ?? 'No remarks provided.');
                                } else {
                                    // Clear data if selection is removed
                                    $set('name', null);
                                    $set('username', null);
                                    $set('eval_score', null);
                                    $set('eval_remarks', null);
                                }
                            })
                            ->dehydrated(false) // Do not save application_id to the Intern table
                            ->required(),

                        // Contextual data: Shown to Admin but not saved to the Intern model
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Placeholder::make('eval_score')
                                    ->label('Interview Score')
                                    ->content(fn ($get) => $get('eval_score') ?: '-'),
                                    
                                Forms\Components\Placeholder::make('eval_remarks')
                                    ->label('Interviewer Remarks')
                                    ->content(fn ($get) => $get('eval_remarks') ?: '-'),
                            ]),
                    ]),

                Forms\Components\Section::make('Intern Account Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('username')
                            ->label('System Username')
                            ->required()
                            ->unique(Intern::class, 'username', ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('password')
                            ->label('Temporary Password')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->maxLength(255)
                            ->helperText('Provide a temporary password for the intern to log in.'),
                    ])->columns(2),
            ]);
            //]);
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
