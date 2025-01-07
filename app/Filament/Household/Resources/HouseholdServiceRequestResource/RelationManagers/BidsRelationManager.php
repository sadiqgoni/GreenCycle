<?php

namespace App\Filament\Household\Resources\HouseholdServiceRequestResource\RelationManagers;

use App\Models\Company;
use App\Models\ServiceRequest;
use App\Models\User;
use DB;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder;


class BidsRelationManager extends RelationManager
{

    protected static string $relationship = 'companyBids';

    protected static ?string $label = 'Bids';

    // protected static ?string $pluralLabel = 'Bids';
    // public function table(Table $table): Table
    // {

    //     return $table
    // ->query(function (Builder $query) {
    //     $query->whereHas('companyBids', function (Builder $query) {
    //         $query->whereNotNull('pivot.bid_amount'); // Filter by the bid_amount in the pivot table
    //     });
    // })
    //         ->columns([
    //             Tables\Columns\TextColumn::make('companyBids.name')
    //                 ->label('Company Name')
    //                 ->sortable()
    //                 ->searchable(),
    //             Tables\Columns\TextColumn::make('companyBids.pivot.bid_amount')
    //                 ->label('Bid Amount')
    //                 ->money('NGN'), // Adjust currency
    //             Tables\Columns\TextColumn::make('companyBids.pivot.notes')
    //                 ->label('Notes'),
    //             Tables\Columns\TextColumn::make('companyBids.pivot.updated_at')
    //                 ->label('Bid Date')
    //                 ->dateTime(),
    //         ])
    //         ->actions([
    //         //     Tables\Actions\Action::make('accept_bid')
    //         //         ->label('Accept Bid')
    //         //         ->action(function ($record) {
    //         //             $serviceRequest = $record->serviceRequest;

    //         //             // Ensure the pivot data exists and update the status
    //         //             if ($record->pivot) {
    //         //                 $record->pivot->update([
    //         //                     'status' => 'accepted',
    //         //                 ]);

    //         //                 $serviceRequest->update([
    //         //                     'status' => 'accepted',
    //         //                     'accepted_company_id' => $record->id,
    //         //                 ]);

    //         //                 Notification::make()
    //         //                     ->title('Bid accepted successfully!')
    //         //                     ->success()
    //         //                     ->send();
    //         //             } else {
    //         //                 throw new \Exception('No bid found for this company.');
    //         //             }
    //         //         })
    //         //         ->requiresConfirmation()
    //         //         ->color('success')
    //         //         ->icon('heroicon-o-check-circle'),
    //         // 
    //         ]);
    // }

    public function table(Table $table): Table
    {

        return $table

            ->columns([
                Tables\Columns\TextColumn::make('company_id')
                    ->label('Company Name')
                    ->formatStateUsing(fn(int $state): string => Company::find($state)?->company_name ?? 'Pending Company')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('bid_amount')
                    ->label('Bid Amount')
                    ->money('NGN'), // Adjust currency as needed
                Tables\Columns\TextColumn::make('notes')
                    ->label('Notes'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Bid Date')
                    ->dateTime(),
            ])

            ->actions([
                Tables\Actions\Action::make('accept_bid')
                ->label('Accept Bid')
                ->action(function ($record) {
                    $serviceRequestId = $record->pivot->service_request_id;
                    $companyId = $record->pivot->company_id;
                    $companyUserId = $record->company_user_id;
                    $bidAmount = $record->pivot->bid_amount;

            
                    if (!$serviceRequestId || !$companyId) {
                        throw new \Exception('Invalid data for bid acceptance.');
                    }
            
                    // Accept this bid
                    DB::table('company_service_requests')
                        ->where('service_request_id', $serviceRequestId)
                        ->where('company_id', $companyId)
                        ->update(['status' => 'accepted',]);
            
                    // Decline all other bids
                    DB::table('company_service_requests')
                        ->where('service_request_id', $serviceRequestId)
                        ->where('company_id', '!=', $companyId)
                        ->update(['status' => 'declined']);
            
                    // Update the service request status to "awaiting_payment"
                    $serviceRequest = ServiceRequest::find($serviceRequestId);
                    $serviceRequest->update([
                        'status' => 'awaiting_payment',
                        'final_amount' => $bidAmount,
                        'accepted_company_id' => $companyId,
                        'company_user_id' => $companyUserId
                    ]);
            
                    Notification::make()
                        ->title('Bid accepted successfully!')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->hidden(fn ($record) => $record->pivot->status === 'declined' || $record->pivot->status === 'accepted'), // Hide if bid is already accepted
            
            ]);
    }
}
