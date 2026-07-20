<?php

namespace App\Filament\Atasan\Resources\Users\Schemas;

use Filament\Schemas\Schema;

use App\Enums\UserRole;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Pengguna')->schema([
                    TextInput::make('name')
                        ->label('Nama Lengkap')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('username')
                        ->label('Username')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context): bool => $context === 'create')
                        ->maxLength(255),
                    Select::make('role')
                        ->label('Role')
                        ->options(collect(UserRole::cases())->mapWithKeys(fn ($r) => [$r->value => $r->label()]))
                        ->required()
                        ->reactive(),
                    Select::make('parent_id')
                        ->label('Induk Akun')
                        ->options(function (Get $get) {
                            $role = $get('role');
                            if ($role === UserRole::KARYAWAN_GUDANG->value) {
                                return \App\Models\User::where('role', UserRole::GUDANG)->pluck('name', 'id');
                            }
                            if ($role === UserRole::KARYAWAN_PERCETAKAN->value) {
                                return \App\Models\User::where('role', UserRole::PERCETAKAN)->pluck('name', 'id');
                            }
                            return [];
                        })
                        ->visible(fn (Get $get) => in_array($get('role'), [UserRole::KARYAWAN_GUDANG->value, UserRole::KARYAWAN_PERCETAKAN->value]))
                        ->required(fn (Get $get) => in_array($get('role'), [UserRole::KARYAWAN_GUDANG->value, UserRole::KARYAWAN_PERCETAKAN->value])),
                ])->columns(2),

                Section::make('Profil Daerah')
                    ->relationship('daerah')
                    ->visible(fn (Get $get) => $get('role') === UserRole::DAERAH->value)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Daerah')
                            ->required(fn (Get $get) => $get('../../role') === UserRole::DAERAH->value),
                        TextInput::make('pic_name')
                            ->label('Nama PIC'),
                        TextInput::make('pic_phone')
                            ->label('No. HP PIC'),
                        TextInput::make('phone')
                            ->label('No. Telepon Daerah'),
                        TextInput::make('address')
                            ->label('Alamat')
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])->columns(2),

                Section::make('Profil Percetakan')
                    ->relationship('percetakan')
                    ->visible(fn (Get $get) => $get('role') === UserRole::PERCETAKAN->value)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Percetakan')
                            ->required(fn (Get $get) => $get('../../role') === UserRole::PERCETAKAN->value),
                        TextInput::make('pic_name')
                            ->label('Nama PIC'),
                        TextInput::make('pic_phone')
                            ->label('No. HP PIC'),
                        TextInput::make('address')
                            ->label('Alamat')
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])->columns(2),

                Section::make('Profil Gudang')
                    ->relationship('gudang')
                    ->visible(fn (Get $get) => $get('role') === UserRole::GUDANG->value)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Gudang')
                            ->required(fn (Get $get) => $get('../../role') === UserRole::GUDANG->value),
                        TextInput::make('pic_name')
                            ->label('Nama PIC'),
                        TextInput::make('pic_phone')
                            ->label('No. HP PIC'),
                        TextInput::make('address')
                            ->label('Alamat')
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])->columns(2),
            ]);
    }
}
