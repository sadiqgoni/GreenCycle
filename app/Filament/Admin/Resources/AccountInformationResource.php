<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AccountInformationResource\Pages;
use App\Filament\Admin\Resources\AccountInformationResource\RelationManagers;
use App\Models\AccountInformation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Company;
use Filament\Infolists\Components\ImageEntry;
use Filament\Notifications\Notification;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AccountInformationResource extends Resource
{
    protected static ?string $model = AccountInformation::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $modelLabel = 'Bank Account';

    protected static ?string $navigationLabel = 'Bank Account';
    protected static ?string $navigationGroup = 'Account Management';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('')
                    ->schema([
                        Forms\Components\TextInput::make('admin_account_name')
                            ->label('Account Name')

                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('admin_account_number')
                            ->label('Account Number')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('admin_bank_name')
                            ->label('Bank Name')
                            ->maxLength(255),


                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('admin_account_name')
                ->color(fn(?Model $record): array => \Filament\Support\Colors\Color::hex(optional($record->tenant)->color ?? '#22e03a'))
                ->weight('bold')
                ->searchable(),
                TextColumn::make('admin_account_number')
                ->color(fn(?Model $record): array => \Filament\Support\Colors\Color::hex(optional($record->tenant)->color ?? '#15803d'))
                ->weight('bold')
                ->searchable(),
                TextColumn::make('admin_bank_name')
                ->color(fn(?Model $record): array => \Filament\Support\Colors\Color::hex(optional($record->tenant)->color ?? '#1261A0'))
                ->weight('bold')
                ->searchable(),
                IconColumn::make('status')
                ->label('Status')
                ->color(fn(string $state): string => match ($state) {
                    'open' => 'success',
                    'closed' => 'danger',
                    default => 'gray',
                })
                ->icon(fn(string $state): string => match ($state) {
                    'open' => 'heroicon-m-check-circle',
                    'closed' => 'heroicon-m-x-circle',
                    default => 'heroicon-m-question-mark-circle',
                })
            ])
            ->actions([
                Tables\Actions\Action::make('status')
                    ->label('Status')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        // Check if the current status is 'open' or 'closed' and toggle it
                        $newStatus = $record->status === 'open' ? 'closed' : 'open';

                        // Update the record's availability_status
                        $record->update(['status' => $newStatus]);

                        // Define the notification message based on the new status
                        $statusMessage = $newStatus === 'open'
                            ? "This account number is now open for transaction"
                            : "This account number is now closed for transaction";
                        Notification::make()
                            ->title('Status Changed')
                            ->success()
                            ->body($statusMessage)
                            ->send();
                    })
                    ->icon('heroicon-o-arrow-left-end-on-rectangle'),

             


            ])
            ->filters([
                //
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
            'index' => Pages\ListAccountInformation::route('/'),
            'create' => Pages\CreateAccountInformation::route('/create'),
            'edit' => Pages\EditAccountInformation::route('/{record}/edit'),
        ];
    }
}
