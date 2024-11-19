<?php
namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Infolists\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Infolists\Infolist;
class UserResource extends Resource
{
    protected static ?string $model = User::class;

    // public static function shouldRegisterNavigation(): bool
    // {
    //     return Filament::getCurrentPanel()?->getId() === 'management';
    // }
    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationGroup = 'Staff Management';

    protected static ?string $modelLabel = 'System Users';
    protected static ?string $navigationLabel = 'System Users';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        // Helper function to format names
        $formatName = function ($state, callable $set, $field) {
            $formatted = ucwords(strtolower($state));
            $set($field, $formatted);
        };

        return $form
            ->schema([
                Section::make('User Details')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->afterStateUpdated(fn($state, $set) => $formatName($state, $set, 'name'))
                            ->maxLength(255)
                            ->placeholder('Enter full name'),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->label('Email Address')
                            ->required()
                            ->placeholder('Enter email address')
                            ->maxLength(255),
                        Forms\Components\Select::make('role')
                            ->label('Role')
                            ->required()
                            ->searchable()
                            ->options([
                                'household' => 'Household',
                                'company' => 'Company',
                                'admin' => 'Admin',                  
                            ])
                            ->placeholder('Select Role'),
                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required()
                            ->placeholder('Enter a secure password')
                            ->maxLength(255)
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Full Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email Address')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('role')
                    ->label('Role')
                    ->colors([
                        'success' => 'Household',
                        'info' => 'Company',
                        'warning' => 'Admin',

                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date Created')
                    ->dateTime()
                    ->sortable(),

            
            ])
            ->filters([
                // Add custom filters if needed
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([

                // Basic Information Section
                \Filament\Infolists\Components\Section::make('Basic Information')
                    ->schema([
                        Split::make([
                            Grid::make(2)
                                ->schema([
                                    TextEntry::make('name')
                                        ->label('Full Name'),

                                    TextEntry::make('email')
                                        ->label('Email Address'),
                                    TextEntry::make('role')
                                        ->label('Role')
                                        ->badge()
                                        ->colors([
                                            'success' => 'Manager',
                                            'info' => 'FrontDesk',
                                            'warning' => 'Housekeeper',
                                            'danger' => 'Restaurant',
                                        ]),
                                        
                                
                                    TextEntry::make('created_at')
                                        ->label('Date Created')
                                        ->dateTime(),

                                    TextEntry::make('updated_at')
                                        ->label('Last Updated')
                                        ->dateTime(),
                                ]),
                        ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
