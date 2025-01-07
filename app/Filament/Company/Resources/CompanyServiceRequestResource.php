<?php

namespace App\Filament\Company\Resources;

use App\Filament\Company\Resources\CompanyServiceRequestResource\Pages;
use App\Filament\Company\Resources\CompanyServiceRequestResource\RelationManagers;
use App\Models\ServiceRequest;
use DB;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompanyServiceRequestResource extends Resource
{
    protected static ?string $model = ServiceRequest::class;
    protected static ?string $navigationGroup = 'Waste Management';

    protected static ?string $modelLabel = 'Waste Service Response';
    protected static ?string $navigationLabel = 'Waste Service Response';
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()
    //         ->where('company_user_id', auth()->id());
    // }


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('companyServiceRequest', function ($query) {
                $query->where('company_user_id', auth()->id())
                    ->whereIn('status', ['pending', 'bid', 'accepted']); // Include only specific statuses
            });
    }



    public static function shouldRegisterNavigation(): bool
    {
        return Filament::getCurrentPanel()?->getId() === 'company';
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')
            ->count();
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('waste_type')
                    ->weight('bold')
                    ->color(fn(?Model $record): array => \Filament\Support\Colors\Color::hex(optional($record->tenant)->color ?? '#22e03a'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('household.name')
                    ->searchable()
                    ->color(fn(?Model $record): array => \Filament\Support\Colors\Color::hex(optional($record->tenant)->color ?? '#1261A0'))
                    ->weight('bold')
                    ->label('Client'),

                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->color(fn(?Model $record): array => \Filament\Support\Colors\Color::hex(optional($record->tenant)->color ?? '#15803d'))
                    ->weight('bold')
                    ->label('Address'),
                Tables\Columns\TextColumn::make('client_number')
                    ->searchable()
                    ->color(fn(?Model $record): array => \Filament\Support\Colors\Color::hex(optional($record->tenant)->color ?? '#19803d'))
                    ->weight('bold')
                    ->label('Phone'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'assigned' => 'warning',
                        'accepted' => 'success',
                        'in_progress' => 'info',
                        'awaiting_payment' => 'warning',
                        'payment_sent' => 'info',
                        'completed' => 'success',
                        'paid' => 'success',
                        'cancelled' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('preferred_date')
                    ->date(),

                Tables\Columns\TextColumn::make('preferred_time')
                    ->time(),
                Tables\Columns\TextColumn::make('final_amount')
                    ->default('pending')
                    ->money('NGN'),
                Tables\Columns\TextColumn::make('company_payout_amount')
                    ->label('Company Balance')
                    ->default('pending')
                    ->money('NGN'),
                Tables\Columns\TextColumn::make('admin_commission_amount')
                    ->label('Admin Balance')

                    ->default('pending')
                    ->money('NGN')
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'assigned' => 'Assigned',
                        'accepted' => 'Accepted',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
                    ]),

            ])
            ->actions([
                Tables\Actions\Action::make('place_bid')
                    ->label('Place Bid')
                    ->form([
                        Forms\Components\TextInput::make('bid_amount')
                            ->label('Bid Amount')
                            ->required()
                            ->numeric()
                            ->minValue(1),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->maxLength(255),
                    ])
                    ->action(function (array $data, $record) {

                        // Save the bid to the pivot table
                        DB::table('company_service_requests')
                            ->updateOrInsert(
                                [
                                    'service_request_id' => $record->id,
                                    'company_user_id' => auth()->id(),
                                ],
                                [
                                    'bid_amount' => $data['bid_amount'],
                                    'notes' => $data['notes'],
                                    'status' => 'bid', // Update status to 'bid'
                                    'updated_at' => now(),
                                ]
                            );

                        Notification::make()
                            ->title('Bid placed successfully!')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->visible(fn($record) => $record->accepted_company_id === null),

                Tables\Actions\Action::make('chosen_company')
                    ->visible(fn($record) => $record->company_user_id === auth()->id())
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\DatePicker::make('scheduled_date')
                            ->default(fn($record) => $record->preferred_date)
                            ->required(),

                        Forms\Components\TimePicker::make('scheduled_time')
                            ->default(fn($record) => $record->preferred_time)
                            ->required(),

                        Forms\Components\TextArea::make('company_notes')
                            ->label('Notes for Customer')
                            ->maxLength(500),

                        Forms\Components\TextInput::make('estimated_duration')
                            ->label('Estimated Duration (hours)')
                            ->numeric()
                            ->required(),
                    ])
                    ->action(function (ServiceRequest $record, array $data): void {
                        $record->update([
                            'scheduled_date' => $data['scheduled_date'],
                            'scheduled_time' => $data['scheduled_time'],
                            'company_notes' => $data['company_notes'],
                            'estimated_duration' => $data['estimated_duration'],
                        ]);
                    }),

                Tables\Actions\Action::make('start_work')
                    ->visible(fn($record) => $record->status === 'paid')
                    ->color('primary')
                    ->icon('heroicon-o-play')
                    ->action(function (ServiceRequest $record): void {
                        $record->update([
                            'status' => 'in_progress',
                            'started_at' => now(),
                        ]);
                    }),

                Tables\Actions\Action::make('complete_work')
                    ->visible(fn($record) => $record->status === 'in_progress')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->form([


                        Forms\Components\Textarea::make('completion_notes')
                            ->label('Work Completion Notes')
                            ->required(),

                        Forms\Components\FileUpload::make('completion_photos')
                            ->label('Completion Photos')
                            ->multiple()
                            ->disk('public')
                            ->preserveFilenames(),
                    ])
                    ->action(function (ServiceRequest $record, array $data): void {
                        $record->update([
                            'status' => 'completed',
                            'completion_notes' => $data['completion_notes'],
                            'completion_photos' => $data['completion_photos'],
                            'completed_at' => now(),
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceRequests::route('/'),
            'create' => Pages\CreateServiceRequest::route('/create'),
            // 'edit' => Pages\EditServiceRequest::route('/{record}/edit'),
        ];
    }
}