<?php

namespace App\Filament\Household\Resources;

use App\Filament\Household\Resources\HouseholdServiceRequestResource\Pages;
use App\Filament\Household\Resources\HouseholdServiceRequestResource\RelationManagers\BidsRelationManager;
use App\Models\AccountInformation;
use App\Models\Company;
use App\Models\Payment;
use App\Models\ServiceRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class HouseholdServiceRequestResource extends Resource
{
    protected static ?string $model = ServiceRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $modelLabel = 'Request Waste Service';

    protected static ?string $navigationLabel = 'Request Waste Service';
    protected static ?string $navigationGroup = 'Requests Management';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('household_id', auth()->id());
    }
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Request Details')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('waste_type')
                        ->label('Waste Type')
                        ->required()
                        ->options([
                            'organic' => 'Organic Waste',
                            'recyclable' => 'Recyclable Waste',
                            'e-waste' => 'Electronic Waste',
                            'hazardous' => 'Hazardous Waste',
                        ]),
                    Forms\Components\Select::make('quantity')
                        ->label('Quantity')
                        ->options([
                            '1-5kg' => '1 - 5 kg',
                            '6-10kg' => '6 - 10 kg',
                            '11-20kg' => '11 - 20 kg',
                            '21-50kg' => '21 - 50 kg',
                            'above_50kg' => 'Above 50 kg',
                        ])
                        ->required(),
                    Forms\Components\Textarea::make('description')
                        ->columnSpan(2)
                        ->maxLength(1000),


                    Forms\Components\DatePicker::make('preferred_date')
                    ,
                    Forms\Components\TimePicker::make('preferred_time')
                    ,
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('waste_type')
                    ->searchable()
                    ->color(fn(?Model $record): array => \Filament\Support\Colors\Color::hex(optional($record->tenant)->color ?? '#22e03a'))
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('quantity')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('accepted_company_id')
                    ->formatStateUsing(fn($state): string => Company::find((int) $state)?->company_name ?? 'Pending Company') // Cast to int if needed
                    ->label('Assigned Company')
                    ->badge()
                    ->default('pending')
                    ->colors([
                        'success' => 'confirmed',
                        'warning' => 'pending',
                        'danger' => 'failed',
                    ]),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'assigned' => 'warning',
                        'accepted' => 'success',
                        'awaiting_payment' => 'warning',
                        'payment_sent' => 'info',
                        'in_progress' => 'info',
                        'completed' => 'success',
                        'paid' => 'success',
                        'cancelled' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('preferred_date')
                    ->date(),
                Tables\Columns\TextColumn::make('preferred_time')
                    ->time(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'assigned' => 'Assigned',
                        'accepted' => 'Accepted',

                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
                    ])
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View Available Companies'),

                Tables\Actions\Action::make('view_account')
                    ->label('Make Payment')
                    ->visible(fn($record) => $record->accepted_company_id)

                    ->modalHeading('Open Account Information')
                    ->modalWidth(\Filament\Support\Enums\MaxWidth::Medium)
                    ->form([
                        Forms\Components\Placeholder::make('amount_to_pay')
                            ->label('Amount to Pay')
                            ->content(fn($record) => '₦' . number_format($record->final_amount, 2)),

                        Forms\Components\Select::make('payment_method')
                            ->options([
                                'credit_card' => 'Credit Card',
                                'bank_transfer' => 'Bank Transfer',
                            ])
                            ->required(),
                    ])
                    ->modalContent(function () {
                        $openAccount = AccountInformation::where('status', 'open')->first();

                        if (!$openAccount) {
                            // Return a Blade view to handle no accounts
                            return view('filament.resources.account-modal', [
                                'account' => null,
                                'errorMessage' => 'No open accounts are available.',
                            ]);
                        }

                        // Return the Blade view with the account details
                        return view('filament.resources.account-modal', [
                            'account' => $openAccount,
                            'errorMessage' => null,
                        ]);
                    })
                    ->action(function (ServiceRequest $record, array $data): void {
                        Payment::create([
                            'service_request_id' => $record->id,
                            'method' => $data['payment_method'],
                            'amount' => $record->final_amount,
                            'status' => 'pending',
                        ]);
                        $record->update(['status' => 'payment_sent']);
                        Notification::make()
                            ->title('Payment marked as completed')
                            ->body('Waiting for admin verification....')
                            ->success()
                            ->send();
                    })

                    ->modalButton('Payment')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('primary'),

                // Tables\Actions\Action::make('cancel_request')
                //     ->visible(fn($record) => in_array($record->status, ['accepted', 'assigned']))
                //     ->requiresConfirmation()
                //     ->action(fn(ServiceRequest $record) => $record->update(['status' => 'cancelled'])),
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
            BidsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceRequests::route('/'),
            'create' => Pages\CreateServiceRequest::route('/create'),
            'view' => Pages\ViewServiceRequest::route('/{record}'),
            'edit' => Pages\EditServiceRequest::route('/{record}/edit'),
        ];
    }
}