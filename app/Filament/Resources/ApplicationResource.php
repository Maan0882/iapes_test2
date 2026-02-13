<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicationResource\Pages;
use App\Filament\Resources\ApplicationResource\RelationManagers;
use App\Models\Application;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\InterviewBatch;
use App\Mail\InterviewScheduledMail;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Filament\Forms\Components\{TextInput, Select, DatePicker, TimePicker, Section};
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use Carbon\Carbon;
use Filament\Tables\Actions\Action;
//use Filament\Forms\Components\Select;


class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Intern Details')
                ->schema([
                    TextInput::make('name')->required()->disabled(),
                    TextInput::make('email')->disabled(),
                    TextInput::make('phone')->disabled(),
                    TextInput::make('college')->disabled(),
                    TextInput::make('degree')->disabled(),
                    TextInput::make('cgpa')->disabled(),
                    TextInput::make('domain')->disabled(),
                ])->columns(2),

                Select::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'Interview Scheduled' => 'Interview Scheduled',
                        'Selected' => 'Selected',
                        'Rejected' => 'Rejected',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('5s') // ⬅ auto refresh every 5 seconds
            ->recordTitleAttribute('name')
            ->modifyQueryUsing(function ($query) {
                $query->with('interviewBatches');
            })
            ->columns([
                TextColumn::make('name')
                ->searchable()
                ->sortable(),

                TextColumn::make('email')
                    ->searchable(),

                TextColumn::make('domain')
                    ->badge()
                    ->color('info'),

                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'Pending',
                        'primary' => 'Interview Scheduled',
                        'success' => 'Selected',
                        'danger' => 'Rejected',
                    ]),

                TextColumn::make('batch')
                    ->label('Batch')
                    ->getStateUsing(function ($record) {
                        if ($record->interviewBatches->isEmpty()) {
                            return '-';
                        }

                        return $record->interviewBatches
                            ->pluck('batch_name')
                            ->join(', ');
                    })
                    ->badge()
                    ->color('info'),

                TextColumn::make('interview_date')
                    ->label('Interview Date')
                    ->getStateUsing(function ($record) {
                        if ($record->interviewBatches->isEmpty()) {
                            return '-';
                        }

                        return $record->interviewBatches
                            ->map(fn ($batch) => \Carbon\Carbon::parse($batch->interview_date)->format('d M Y'))
                            ->join(', ');
                    })
            ])
            ->filters([
                SelectFilter::make('status')
                ->options([
                    'Pending' => 'Pending',
                    'Interview Scheduled' => 'Interview Scheduled',
                ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('scheduleInterview')
                    ->label('Schedule Interview')
                    ->icon('heroicon-o-calendar')
                    ->color('primary')
                    ->action(function ($records, $data) {
                        // 1️⃣ Check if any record is not shortlisted
                        $invalid = $records->filter(function ($record) {
                        return $record->status !== 'shortlisted';
                        });

                        if ($invalid->isNotEmpty()) {
                        \Filament\Notifications\Notification::make()
                        ->title('Only shortlisted interns can be scheduled.')
                        ->danger()
                        ->send();

                        return;
                        }

                        // 2️⃣ Check if already scheduled
                        $alreadyScheduled = $records->filter(function ($record) {
                        return $record->interviewBatches()->exists();
                        });

                        if ($alreadyScheduled->isNotEmpty()) {
                        \Filament\Notifications\Notification::make()
                        ->title('Some interns are already scheduled.')
                        ->danger()
                        ->send();

                        return;
                        }
                    })
                    ->form([

                        Forms\Components\DatePicker::make('interview_date')
                            ->required(),

                        Forms\Components\TimePicker::make('start_time')
                            ->required(),

                        Forms\Components\TextInput::make('batch_size')
                            ->numeric()
                            ->default(5)
                            ->required(),

                        Forms\Components\TextInput::make('location')
                            ->required(),
                    ])
                    ->action(function ($records, $data) {

                        $batchSize = $data['batch_size'];
                        $chunks = $records->chunk($batchSize);

                        $startTime = \Carbon\Carbon::parse($data['start_time']);

                        foreach ($chunks as $index => $chunk) {

                            $batch = \App\Models\InterviewBatch::create([
                                'batch_name' => 'Batch ' . ( \App\Models\InterviewBatch::count() + 1 ),
                                'interview_date' => $data['interview_date'],
                                'start_time' => $startTime->format('H:i:s'),
                                'end_time' => $startTime->copy()->addHour()->format('H:i:s'),
                                'batch_size' => $batchSize,
                                'location' => $data['location'],
                            ]);

                            foreach ($chunk as $application) {

                                $batch->applications()->attach($application->id);

                                $application->update([
                                    'status' => 'interview_scheduled'
                                ]);

                                // \Mail::to($application->email)
                                //     ->queue(new \App\Mail\InterviewScheduledMail($application, $batch));
                            }

                            $startTime->addHour();
                        }
                    })
                    ->requiresConfirmation(),

                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListApplications::route('/'),
            'create' => Pages\CreateApplication::route('/create'),
            'edit' => Pages\EditApplication::route('/{record}/edit'),
        ];
    }
}
