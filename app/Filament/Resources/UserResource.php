<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationLabel = ' Manajemen User';

    public static function canViewAny(): bool
    {
        return Auth::user()->role === 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('logo')
                    ->image()
                    ->label('Logo Toko')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('Nama Toko')
                    ->required(),
                Forms\Components\TextInput::make('username')
                    ->label('Username')
                    ->hint('Minimal 5 karakter tanpa spasi')
                    ->minLength(5)
                    ->unique(ignoreRecord: true)
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->required(),
                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->hint('minimal 8 karakter tanpa spasi')
                    ->minLength(8)
                    ->dehydrated(fn($state) => filled($state)) // ⬅️ PENTING
                    ->dehydrateStateUsing(fn($state) => bcrypt($state))
                    ->required(fn(string $context) => $context === 'create'),
                Forms\Components\Select::make('role')
                    ->label('Role')
                    ->options([
                        'admin' =>  'Admin',
                        'store' =>  'Toko'
                    ])
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo Toko'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Toko'),
                Tables\Columns\TextColumn::make('username')
                    ->label('Username'),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email'),
                Tables\Columns\TextColumn::make('role')
                    ->label('Role'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Mendaftar'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
