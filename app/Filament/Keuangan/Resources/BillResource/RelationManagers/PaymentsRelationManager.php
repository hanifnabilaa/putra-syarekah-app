<?php

namespace App\Filament\Keuangan\Resources\BillResource\RelationManagers;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'Riwayat Transaksi';
    
    protected static ?string $modelLabel = 'Transaksi';

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->maxLength(255),
            ]);
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
                    ->label('Petugas')
                    ->sortable(),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Catatan')
                    ->wrap(),

                Tables\Columns\ImageColumn::make('proof_image')
                    ->label('Bukti')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(url('https://ui-avatars.com/api/?name=No+Image&color=7F9CF5&background=EBF4FF')),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Rekam (Sistem)')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // We use BillResource actions for adding payment, so read only here for now
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                \Filament\Actions\Action::make('Lihat Bukti')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Payment $record): ?string => $record->proof_image_url)
                    ->openUrlInNewTab()
                    ->visible(fn (Payment $record): bool => (bool)$record->proof_image)
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('payment_date', 'desc');
    }
}
