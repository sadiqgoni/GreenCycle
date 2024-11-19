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
                Tables\Columns\TextColumn::make('company.name')
                    ->searchable()
                    ->badge()
                    ->default('pending')
                    ->label('Assigned Company'),

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
                Tables\Columns\TextColumn::make('final_amount')
                    ->money('NGN'),
                // ->visible(fn($record) => $record && $record->status === 'completed'),
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
                Tables\Actions\Action::make('assign_company')
                    ->visible(fn($record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\Select::make('company_id')
                            ->label('Select Company')
                            ->searchable()
                            ->preload()
                            ->options(fn() => Company::where('verification_status', 'verified')
                                ->where('availability_status', 'open')
                                ->pluck('company_name', 'id'))
                            ->required(),

                    ])
                    ->action(function (ServiceRequest $record, array $data): void {
                        $company = Company::find($data['company_id']);
                        if ($company) {
                            $record->update([
                                'company_id' => $data['company_id'],
                                'company_user_id' => $company->user_id, // Set company_user_id
                                'status' => 'assigned',
                            ]);
                        } else {
                            throw new \Exception('Company not found.');
                        }
                    }),
                Tables\Actions\Action::make('confirm_payment')
                    ->visible(fn($record) => $record->payment && $record->payment->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (ServiceRequest $record): void {
                        $payment = $record->payment; // Access the associated payment
                        $payment->update([
                            'status' => 'confirmed',
                            'paid_at' => now(),
                        ]);

                        // Optional: Update ServiceRequest status if needed
                        $record->update([
                            'payment_status' => 'paid',
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
                    ->form([
                        Forms\Components\TextInput::make('commission_percentage')
                            ->label('Commission Percentage (%)')
                            ->default(10), // Default commission is 10%
                        
                        Forms\Components\Placeholder::make('commission_preview')
                            ->label('Commission Amount')
                            ->content(function ($get, $record) {
                                $percentage = $get('commission_percentage') ?? 10;
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
            'edit' => Pages\EditServiceRequest::route('/{record}/edit'),
        ];
    }
}