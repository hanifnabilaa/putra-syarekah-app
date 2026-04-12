<?php

namespace App\Filament\Percetakan\Resources;

use App\Enums\UserRole;
use App\Filament\Percetakan\Resources\DaerahResource\Pages;
use App\Models\Daerah;
use App\Models\User;
use Filament\Forms;
use Filament\Schemas\Schema;

use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DaerahResource extends Resource
{
    protected static ?string $model = Daerah::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel = 'Manajemen Daerah';
    protected static ?string $modelLabel = 'Daerah';
    protected static ?string $pluralModelLabel = 'Daerah';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            \Filament\Schemas\Components\Section::make('Informasi Daerah')->schema([
                \Filament\Schemas\Components\TextInput::make('name')
                    ->label('Nama Daerah')
                    ->required()
                    ->maxLength(255),

                \Filament\Schemas\Components\TextInput::make('pic_name')
                    ->label('Nama Penanggung Jawab')
                    ->required()
                    ->maxLength(255),

                \Filament\Schemas\Components\TextInput::make('pic_phone')
                    ->label('No HP Penanggung Jawab')
                    ->tel()
                    ->required()
                    ->maxLength(20),

                \Filament\Schemas\Components\TextInput::make('phone')
                    ->label('No Telepon Daerah')
                    ->tel()
                    ->maxLength(20),

                \Filament\Schemas\Components\Textarea::make('address')
                    ->label('Alamat Default')
                    ->rows(2)
                    ->columnSpanFull(),

                \Filament\Schemas\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ])->columns(2),

            \Filament\Schemas\Components\Section::make('Akun Login')->schema([
                \Filament\Schemas\Components\TextInput::make('user.name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255)
                    ->hiddenOn('edit'),

                \Filament\Schemas\Components\TextInput::make('user.username')
                    ->label('Username')
                    ->required()
                    ->unique(table: 'users', column: 'username', ignorable: fn ($record) => $record?->user)
                    ->maxLength(50)
                    ->hiddenOn('edit'),

                \Filament\Schemas\Components\TextInput::make('user.email')
                    ->label('Email')
                    ->email()
                    ->unique(table: 'users', column: 'email', ignorable: fn ($record) => $record?->user)
                    ->maxLength(255)
                    ->hiddenOn('edit'),

                \Filament\Schemas\Components\TextInput::make('user.password')
                    ->label('Password')
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->hiddenOn('edit'),
            ])->hiddenOn('edit'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Daerah')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('pic_name')
                    ->label('Penanggung Jawab')
                    ->searchable(),

                Tables\Columns\TextColumn::make('pic_phone')
                    ->label('No HP PJ'),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email Login'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                Tables\Columns\TextColumn::make('orders_count')
                    ->label('Pesanan')
                    ->counts('orders'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Status Aktif'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),

                \Filament\Actions\Action::make('toggle_active')
                    ->label(fn (Daerah $record) => $record->is_active ? 'Nonaktifkan' : 'Aktifkan')
                    ->icon(fn (Daerah $record) => $record->is_active ? 'heroicon-m-eye-slash' : 'heroicon-m-eye')
                    ->color(fn (Daerah $record) => $record->is_active ? 'warning' : 'success')
                    ->action(fn (Daerah $record) => $record->update(['is_active' => ! $record->is_active])),

                \Filament\Actions\ViewAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDaerahs::route('/'),
            'create' => Pages\CreateDaerah::route('/create'),
            'edit'   => Pages\EditDaerah::route('/{record}/edit'),
            'view'   => Pages\ViewDaerah::route('/{record}'),
        ];
    }
}
