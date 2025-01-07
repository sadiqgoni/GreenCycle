<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AdminServiceRequestResource\Pages;
use App\Filament\Admin\Resources\AdminServiceRequestResource\RelationManagers;
use App\Models\Company;
use App\Models\ServiceRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdminServiceRequestResource extends Resource
{
    protected static ?string $model = ServiceRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $modelLabel = 'Service Request';

    protected static ?string $navigationLabel = 'Service Request';
    protected static ?string $navigationGroup = 'Request Management';
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
                    ->label('Client'),
                    Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->label('Address'),
                Tables\Columns\TextColumn::make('client_number')
                    ->searchable()
                    ->label('Phone'),
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
                Tables\Columns\TextColumn::make('final_amount')
                    ->money('NGN'),
                Tables\Columns\BadgeColumn::make('payment.status')
                    ->label('Payment Status')
                    ->badge()
                    ->default('pending')
                    ->colors([
                        'success' => 'confirmed',
                        'warning' => 'pending',
                        'danger' => 'failed',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
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
                    ]),

            ])
            ->actions([

                Tables\Actions\Action::make('confirm_payment')
                    ->visible(fn($record) => $record->payment && $record->payment->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (ServiceRequest $record): void {
                        $payment = $record->payment;
                        $payment->update([
                            'status' => 'confirmed',
                            'paid_at' => now(),
                        ]);
                        $record->update(attributes: [
                            'status' => 'paid',
                        ]);
                    }),
                Tables\Actions\Action::make('process_commission')
                    ->visible(
                        fn($record) =>
                        $record->status === 'completed' &&
                        $record->payment &&
                        $record->payment->status === 'confirmed' && // Ensure payment is confirmed
                        !$record->commission_paid_at
                    )
                    ->modalWidth(\Filament\Support\Enums\MaxWidth::Medium)
                    ->modalButton('Payment')
                    ->form([
                        Forms\Components\TextInput::make('commission_percentage')
                            ->label('Commission Percentage (%)')
                            ->default(10)
                            ->numeric()
                            ->reactive() // Make it reactive
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                $final_amount = (float) preg_replace('/[^0-9.]/', '', $get('final_amount'));
                                $set('commission_preview', $final_amount * ($state / 100)); // Update preview dynamically
                            }),
                        Forms\Components\Placeholder::make('commission_preview')
                            ->label('Commission Amount')
                            ->content(function ($state, callable $set, $record) {
                                $amount = $record->final_amount;

                                return '₦' . number_format($amount, 2);
                            }),

                        Forms\Components\Placeholder::make('company_payout')
                            ->label('Company Payout Amount')
                            ->content(function ($get, $record) {
                                $percentage = (float) preg_replace('/[^0-9.]/', '', $get('commission_percentage'));

                                // $percentage = $get('commission_percentage') ?? 10;
                                $amount = $record->final_amount * ($percentage / 100);
                                return '₦' . number_format($amount, 2);
                            }),
                    ])
                    ->action(function (ServiceRequest $record, array $data): void {
                        $commissionPercentage = $data['commission_percentage'];
                        $commissionAmount = $record->final_amount * ($commissionPercentage / 100);
                        $companyPayout = $record->final_amount - $commissionAmount;

                        // Update the ServiceRequest with commission details
                        $record->update([
                            'admin_commission_percentage' => $commissionPercentage,
                            'admin_commission_amount' => $commissionAmount,
                            'company_payout_amount' => $companyPayout,
                            'commission_paid_at' => now(),
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