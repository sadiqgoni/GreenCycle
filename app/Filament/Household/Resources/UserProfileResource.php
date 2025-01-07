<?php

namespace App\Filament\Household\Resources;

use App\Filament\Household\Resources\UserProfileResource\Pages;
use App\Filament\Household\Resources\UserProfileResource\RelationManagers;
use App\Models\UserProfile;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserProfileResource extends Resource
{
    protected static ?string $model = UserProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Profile Config';
    protected static ?string $modelLabel = 'User Profile';

    protected static ?string $navigationLabel = 'User Profile';
    public static function form(Form $form): Form
    {
        return $form
     
        ->schema([
            Forms\Components\Section::make('Request Details')
            ->columns(2)
            ->schema([
                Forms\Components\TextInput::make('name')
                   
                    
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->maxLength(255),
                Forms\Components\Textarea::make('address')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('local_government')
                    ->maxLength(length: 255),
                Forms\Components\TextInput::make('account_number')
                    ->maxLength(255),
                Forms\Components\TextInput::make('account_name')
                    ->maxLength(255),
            ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->color(fn(?Model $record): array => \Filament\Support\Colors\Color::hex(optional($record->tenant)->color ?? '#22e03a'))
                    ->weight('bold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                ->color(fn(?Model $record): array => \Filament\Support\Colors\Color::hex(optional($record->tenant)->color ?? '#1261A0'))
                ->weight('bold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('local_government')
                
                    ->searchable(),
                Tables\Columns\TextColumn::make('account_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('account_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),

                Tables\Actions\DeleteAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function shouldRegisterNavigation(): bool
    {
        return Filament::getCurrentPanel()?->getId() === 'household';
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
            'index' => Pages\ListUserProfiles::route('/'),
            'create' => Pages\CreateUserProfile::route('/create'),
            'edit' => Pages\EditUserProfile::route('/{record}/edit'),
        ];
    }
}
