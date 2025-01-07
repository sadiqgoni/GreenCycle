<?php

namespace App\Filament\Company\Resources;

use App\Filament\Company\Resources\CompanyResource\Pages;
use App\Filament\Company\Resources\CompanyResource\RelationManagers;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $modelLabel = 'Company Profile';
    protected static ?string $navigationLabel = 'Company Profile';
    public static function getEloquentQuery(): Builder
    {
        // Check if the user is an admin
        if (auth()->user()->role === 'admin') {
            // If the user is an admin, return all records (no filtering)
            return parent::getEloquentQuery();
        }

        // If the user is not an admin, only show their own records
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Company Details')
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->label('Company Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('ceo_name')
                            ->label('CEO Name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('ceo_email')
                            ->label('CEO Email')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('contact_info')
                            ->label('Contact Information')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->maxLength(500),
                        Forms\Components\TextInput::make('registration_number')
                            ->label('Registration Number')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('tax_number')
                            ->label('Tax Number')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('service_radius')
                            ->label('Service Radius'),
                        Forms\Components\Textarea::make('bank_details')
                            ->label('Bank Details'),
                        Forms\Components\FileUpload::make('image')
                            ->label('Brand Profile')


                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Brand Profile')
                    ->square()
                    ->size(40),
                TextColumn::make('company_name')
                    ->label('Company Name')
                    ->color(fn(?Model $record): array => \Filament\Support\Colors\Color::hex(optional($record->tenant)->color ?? '#22e03a'))
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('ceo_name')
                    ->label('CEO Name')
                    ->color(fn(?Model $record): array => \Filament\Support\Colors\Color::hex(optional($record->tenant)->color ?? '#15803d'))
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('contact_info')
                    ->label('Contact Info')
                    ->color(fn(?Model $record): array => \Filament\Support\Colors\Color::hex(optional($record->tenant)->color ?? '#1261A0'))
                    ->weight('bold')
                    ->searchable(),
                BadgeColumn::make('verification_status')
                    ->label('Verification')
                    ->color(fn(string $state): string => match ($state) {
                        'rejected' => 'danger',
                        'verified' => 'success',
                        'pending' => 'warning',
                    })
                    ->sortable(),

                IconColumn::make('availability_status')
                    ->label('Availability Status')
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
                    ->visible(fn($record, $livewire) => auth()->user()->role === 'admin'),


            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('availability_status')
                    ->label('Availability Status')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        // Check if the current status is 'open' or 'closed' and toggle it
                        $newStatus = $record->availability_status === 'open' ? 'closed' : 'open';

                        // Update the record's availability_status
                        $record->update(['availability_status' => $newStatus]);

                        // Define the notification message based on the new status
                        $statusMessage = $newStatus === 'open'
                            ? "Your Company is now open for operations"
                            : "Your Company is now closed for operations";
                        Notification::make()
                            ->title('Availability Status Changed')
                            ->success()
                            ->body($statusMessage)
                            ->send();
                    })
                    ->visible(fn($record) => auth()->user()->role === 'company')
                    ->icon('heroicon-o-arrow-left-end-on-rectangle'),

                Tables\Actions\EditAction::make()
                    ->visible(fn($record, ) => auth()->user()->role === 'company'),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->visible(fn($record, ) => auth()->user()->role === 'admin')
                    ->action(function ($record) {
                        $record->update(['verification_status' => 'verified']);
                        Notification::make()
                            ->title('Company Approved')
                            ->success()
                            ->body($record->company_name . ' Company has been approved and ready for operation!')
                            ->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->visible(fn($record, $livewire) => auth()->user()->role === 'admin') // Visibility for admin role
                    ->form([
                        Forms\Components\Textarea::make('rejection_note')
                            ->label('Rejection Note')
                            ->required()
                            ->placeholder('Provide a reason for rejection.'),
                    ])
                    ->action(function ($record, $data) {
                        $record->update([
                            'verification_status' => 'rejected', // Update verification_status
                            'rejection_note' => $data['rejection_note'], // Save the rejection note
                        ]);

                        Notification::make()
                            ->title('Company Rejected')
                            ->body('The rejection reason has been sent to the company.')
                            ->warning()
                            ->send();
                    }),


            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // General Information Section
                \Filament\Infolists\Components\Section::make('General Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('company_name')
                                    ->label('Company Name'),

                                TextEntry::make('ceo_name')
                                    ->label('CEO Name'),
                                TextEntry::make('contact_info')
                                    ->label('Contact Information'),
                                TextEntry::make('ceo_email')
                                    ->label('CEO Email'),
                                ImageEntry::make('image')
                                    ->label('Brand Profile'),

                            ]),
                    ]),

                // Business Details Section
                \Filament\Infolists\Components\Section::make('Business Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('registration_number')
                                    ->label('Registration Number'),

                                TextEntry::make('tax_number')
                                    ->label('Tax Number'),

                                TextEntry::make('availability_status')
                                    ->label('Availability Status')
                                    ->badge()
                                    ->badge()
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
                                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),
                                TextEntry::make('verification_status')
                                    ->label('Verification Status')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'pending' => 'warning',
                                        'accepted' => 'success',
                                        'rejected' => 'danger',
                                        default => 'gray',
                                    })
                                    ->icon(fn(string $state): string => match ($state) {
                                        'pending' => 'heroicon-m-clock',
                                        'accepted' => 'heroicon-m-check-circle',
                                        'rejected' => 'heroicon-m-x-circle',
                                        default => 'heroicon-m-question-mark-circle',
                                    })
                                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),
                            ]),
                    ]),

                // Additional Details Section
                \Filament\Infolists\Components\Section::make('Additional Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('service_radius')
                                    ->label('Service Radius'),

                                TextEntry::make('commission_rate')
                                    ->default(10)
                                    ->label('Commission Rate (%)'),

                                TextEntry::make('description')
                                    ->label('Description'),

                                TextEntry::make('bank_details')
                                    ->label('Bank Details'),
                            ]),
                    ]),

                // Additional Details Section
                \Filament\Infolists\Components\Section::make('Rejections Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('rejection_note')
                                    ->label('Rejection Note'),
                            ]),
                    ])->visible(fn($record) => $record->rejection_note),


                // Metadata Section
                \Filament\Infolists\Components\Section::make('Metadata')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime(),

                                TextEntry::make('updated_at')
                                    ->label('Updated At')
                                    ->dateTime(),
                            ]),
                    ]),
            ]);
    }

    public static function getNavigationGroup(): ?string
    {
        if (auth()->user()->role === 'admin') {
            return __('Company Management');

        }
        return __('Profile Configuration');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
            'view' => Pages\ViewCompany::route('/{record}'),

        ];
    }
}
