<?php

namespace App\Filament\Keuangan\Resources;

use App\Enums\BillStatus;
use App\Filament\Keuangan\Resources\BillResource\Pages;
use App\Models\Bill;
use App\Services\BillService;
use Filament\Forms;
use Filament\Schemas\Schema;

use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BillResource extends Resource
{
    protected static ?string $model = Bill::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Manajemen Tagihan';
    protected static ?string $modelLabel = 'Tagihan';
    protected static ?string $pluralModelLabel = 'Tagihan';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            \Filament\Schemas\Components\Section::make('Info Tagihan')->schema([
                \Filament\Schemas\Components\TextInput::make('order.order_code')->label('Kode Pesanan')->disabled(),
                \Filament\Schemas\Components\TextInput::make('total_bill')->label('Total Tagihan')->prefix('Rp')->disabled(),
                \Filament\Schemas\Components\TextInput::make('total_paid')->label('Total Dibayar')->prefix('Rp')->disabled(),
                \Filament\Schemas\Components\TextInput::make('remaining_bill')->label('Sisa Tagihan')->prefix('Rp')->disabled(),
                \Filament\Schemas\Components\TextInput::make('status')->label('Status')->disabled(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order.order_code')
                    ->label('Kode Pesanan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('order.daerah.name')
                    ->label('Daerah')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('order.created_at')
                    ->label('Tgl Pesanan')
                    ->date('d M Y'),

                Tables\Columns\TextColumn::make('total_bill')
                    ->label('Total Tagihan')
                    ->money('IDR'),

                Tables\Columns\TextColumn::make('total_paid')
                    ->label('Dibayar')
                    ->money('IDR'),

                Tables\Columns\TextColumn::make('remaining_bill')
                    ->label('Sisa')
                    ->money('IDR')
                    ->color(fn ($state) => (float) $state > 0 ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof BillStatus ? $state->label() : $state)
                    ->color(fn ($state) => $state instanceof BillStatus ? $state->color() : 'gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(BillStatus::cases())->mapWithKeys(fn ($e) => [$e->value => $e->label()])),

                Tables\Filters\SelectFilter::make('daerah')
                    ->label('Daerah')
                    ->relationship('order.daerah', 'name'),
            ])
            ->actions([
                \Filament\Actions\Action::make('add_payment')
                    ->label('Konfirmasi Pembayaran')
                    ->icon('heroicon-m-currency-dollar')
                    ->color('success')
                    ->visible(fn (Bill $record) => $record->status !== BillStatus::PAID)
                    ->form([
                        \Filament\Schemas\Components\TextInput::make('amount')
                            ->label('Jumlah Dibayar (Rp)')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(1),
                        \Filament\Schemas\Components\DatePicker::make('payment_date')
                            ->label('Tanggal Pembayaran')
                            ->required()
                            ->default(now()),
                        \Filament\Schemas\Components\Textarea::make('notes')
                            ->label('Keterangan (misal: Transfer BCA, Tunai)')
                            ->rows(2),
                    ])
                    ->action(function (Bill $record, array $data): void {
                        app(BillService::class)->addPayment(
                            $record,
                            (float) $data['amount'],
                            auth()->id(),
                            $data['notes'] ?? null,
                            $data['payment_date'],
                        );
                        Notification::make()->title('Pembayaran dikonfirmasi')->success()->send();
                    }),

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
            'index' => Pages\ListBills::route('/'),
            'view'  => Pages\ViewBill::route('/{record}'),
        ];
    }
}
