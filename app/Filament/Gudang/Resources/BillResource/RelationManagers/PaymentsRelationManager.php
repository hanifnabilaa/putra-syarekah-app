<?php

namespace App\Filament\Gudang\Resources\BillResource\RelationManagers;

use App\Models\Payment;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'Riwayat Transaksi Pembayaran';

    protected static ?string $modelLabel = 'Pembayaran';

    public function form(Schema $form): Schema
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Tgl Bayar')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Nominal')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('confirmedBy.name')
                    ->label('Dikonfirmasi Oleh')
                    ->sortable(),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Keterangan / Catatan')
                    ->wrap(),

                Tables\Columns\ImageColumn::make('proof_image')
                    ->label('Bukti')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(url('https://ui-avatars.com/api/?name=No+Image&color=7F9CF5&background=EBF4FF')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Rekam')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([])
            ->actions([
                \Filament\Actions\Action::make('Lihat Bukti')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Payment $record): ?string => $record->proof_image_url)
                    ->openUrlInNewTab()
                    ->visible(fn (Payment $record): bool => (bool)$record->proof_image),
            ])
            ->bulkActions([])
            ->defaultSort('payment_date', 'desc');
    }
}
