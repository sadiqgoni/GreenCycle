<?php

namespace App\Filament\Household\Resources;

use App\Filament\Household\Resources\HouseholdServiceRequestResource\Pages;
use App\Filament\Household\Resources\HouseholdServiceRequestResource\RelationManagers;
use App\Models\Payment;
use App\Models\ServiceRequest;
use Auth;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HouseholdServiceRequestResource extends Resource
{
    protected static ?string $model = ServiceRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $modelLabel = 'Request Waste Service';

    protected static ?string $navigationLabel = 'Request Waste Service';
    protected static ?string $navigationGroup = 'Household';

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
                    Forms\Components\TextInput::make('waste_type')
                        ->label('Waste Type')
                        ->required()
                        ->columnSpan(2)
                        ->maxLength(255),

                    Forms\Components\Textarea::make('description')
                        ->columnSpan(2)
                        ->maxLength(1000),
                    Forms\Components\TextInput::make('quantity'),

                    Forms\Components\DatePicker::make('preferred_date')
                        ->required(),

                    Forms\Components\TimePicker::make('preferred_time')
                        ->required(),
                        Forms\Components\TextInput::make('client_name')
                        ->label('Phone Number')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('address')
                        ->required()
                        ->maxLength(255),
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('waste_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity'),
                Tables\Columns\TextColumn::make('company.name')          
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
                        'in_progress' => 'info',
                        'completed' => 'success',
                        'paid' => 'success',
                        'cancelled' => 'danger',
                    }),
                Tables\Columns\BadgeColumn::make('payment.status')
                    ->label('Payment Status')
                    ->badge()
                    ->default('pending')
                    ->colors([
                        'success' => 'confirmed',
                        'warning' => 'pending',
                        'danger' => 'failed',
                    ]),
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

                Tables\Actions\Action::make('cancel_request')
                    ->visible(fn($record) => in_array($record->status, ['accepted', 'assigned']))
                    ->requiresConfirmation()
                    ->action(fn(ServiceRequest $record) => $record->update(['status' => 'cancelled'])),
                Tables\Actions\Action::make('process_payment')
                    ->visible(fn($record) => $record->status === 'completed' && !$record->payment)
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
                    ->action(function (ServiceRequest $record, array $data): void {
                        Payment::create([
                            'service_request_id' => $record->id,
                            'method' => $data['payment_method'],
                            'amount' => $record->final_amount,
                            'status' => 'pending', // Payment awaiting admin confirmation
                        ]);
                    })


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
            'edit' => Pages\EditServiceRequest::route('/{record}/edit'),
        ];
    }
}