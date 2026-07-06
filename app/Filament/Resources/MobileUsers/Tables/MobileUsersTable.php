<?php

namespace App\Filament\Resources\MobileUsers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MobileUsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('username')
                    ->label('Username')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->searchable(),

                TextColumn::make('divisi')
                    ->label('Divisi')
                    ->badge()
                    ->placeholder('-')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'Admin' => 'Admin',
                        'Asisten Lapangan' => 'Asisten Lapangan',
                        'Ahli Gizi' => 'Ahli Gizi',
                        'Kepala SPPG' => 'Kepala SPPG',
                        'Relawan' => 'Relawan',
                        'Petugas Divisi' => 'Petugas Divisi',
                        'Kepala Divisi' => 'Kepala Divisi',
                    ]),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Aktif' => 'Aktif',
                        'Nonaktif' => 'Nonaktif',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
