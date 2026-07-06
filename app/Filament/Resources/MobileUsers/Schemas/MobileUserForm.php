<?php

namespace App\Filament\Resources\MobileUsers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class MobileUserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Akun Aplikasi')
                    ->description('Data ini dipakai untuk login aplikasi Android melalui API /api/exec.')
                    ->schema([
                        TextInput::make('username')
                            ->label('Username')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
                           ->required(fn ($operation): bool => $operation === 'create')
                            ->helperText('Kosongkan saat edit jika password tidak ingin diubah.'),

                        TextInput::make('nama')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        Select::make('role')
                            ->label('Role')
                            ->required()
                            ->options([
                                'Admin' => 'Admin',
                                'Asisten Lapangan' => 'Asisten Lapangan',
                                'Ahli Gizi' => 'Ahli Gizi',
                                'Kepala SPPG' => 'Kepala SPPG',
                                'Relawan' => 'Relawan',
                                'Petugas Divisi' => 'Petugas Divisi',
                                'Kepala Divisi' => 'Kepala Divisi',
                            ])
                            ->searchable(),

                        Select::make('divisi')
                            ->label('Divisi')
                            ->options([
                                'Asisten Lapangan' => 'Asisten Lapangan',
                                'Persiapan' => 'Persiapan',
                                'Pengolahan' => 'Pengolahan',
                                'Pemorsian' => 'Pemorsian',
                                'Distribusi' => 'Distribusi',
                                'Pencucian' => 'Pencucian',
                                'Kebersihan' => 'Kebersihan',
                            ])
                            ->searchable()
                            ->nullable(),

                        Select::make('status')
                            ->label('Status')
                            ->required()
                            ->default('Aktif')
                            ->options([
                                'Aktif' => 'Aktif',
                                'Nonaktif' => 'Nonaktif',
                            ]),
                    ])
                    ->columns(2),
            ]);
    }
}
