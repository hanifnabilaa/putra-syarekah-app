<?php

namespace App\Filament\Percetakan\Resources;

use App\Enums\OrderStatus;
use App\Enums\ShipmentStatus;
use App\Filament\Percetakan\Resources\ShipmentResource\Pages;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shipment;
use App\Services\ShipmentService;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\ImageEntry;

use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ShipmentResource extends Resource
{
    protected static ?string $model = Shipment::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Manajemen Pengiriman';
    protected static ?string $modelLabel = 'Pengiriman';
    protected static ?string $pluralModelLabel = 'Pengiriman';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Section::make('Pilih Pesanan')->schema([
                Select::make('order_id')
                    ->label('Pesanan')
                    ->options(
                        Order::where('status', OrderStatus::APPROVED)
                            ->with('daerah')
                            ->get()
                            ->mapWithKeys(fn ($o) => [$o->id => "{$o->order_code} — {$o->daerah->name}"])
                    )
                    ->required()
                    ->reactive()
                    ->searchable(),

                Select::make('method')
                    ->label('Metode')
                    ->options(['shipping' => 'Dikirim', 'pickup' => 'Ambil Sendiri'])
                    ->reactive()
                    ->required(),

                Select::make('gudang_id')
                    ->label('Pilih Gudang Tujuan')
                    ->options(\App\Models\User::where('role', \App\Enums\UserRole::GUDANG)->pluck('name', 'id'))
                    ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('method') === 'pickup')
                    ->required(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('method') === 'pickup')
                    ->dehydrated(false),

                Textarea::make('shipping_address')
                    ->label('Alamat Pengiriman')
                    ->hidden(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('method') === 'pickup')
                    ->rows(2),

                DatePicker::make('shipping_date')
                    ->label('Tanggal Kirim'),

                FileUpload::make('proof_of_delivery')
                    ->label('Foto Bukti Pengiriman (Opsional)')
                    ->image()
                    ->directory('shipment-proofs')
                    ->nullable(),

                Textarea::make('notes')
                    ->label('Keterangan')
                    ->rows(2),

            ])->columns(2),

            Section::make('Detail Barang Dikirim')->schema([
                \Filament\Forms\Components\Repeater::make('items')
                    ->relationship('items')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('orderItem.product.name')
                            ->label('Produk'),
                        \Filament\Forms\Components\TextInput::make('quantity')
                            ->label('Jumlah Dikirim'),
                    ])->columns(2)
            ])->hiddenOn(['create', 'edit']),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('queue_order')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('order.order_code')
                    ->label('Kode Pesanan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('order.daerah.name')
                    ->label('Daerah')
                    ->searchable(),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Jml Item')
                    ->counts('items'),

                Tables\Columns\TextColumn::make('method')
                    ->label('Metode')
                    ->formatStateUsing(fn ($state) => $state instanceof \App\Enums\ShipmentMethod ? $state->label() : $state),

                Tables\Columns\TextColumn::make('shipping_date')
                    ->label('Tgl Kirim')
                    ->date('d M Y'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof ShipmentStatus ? $state->label() : $state)
                    ->color(fn ($state) => $state instanceof ShipmentStatus ? $state->color() : 'gray'),
            ])
            ->defaultSort('queue_order')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(ShipmentStatus::cases())->mapWithKeys(fn ($e) => [$e->value => $e->label()])),
            ])
            ->actions([
                \Filament\Actions\Action::make('mark_shipped')
                    ->label('Tandai Dikirim')
                    ->icon('heroicon-m-truck')
                    ->color('warning')
                    ->visible(fn (Shipment $record) => $record->status === ShipmentStatus::PENDING)
                    ->requiresConfirmation()
                    ->form([
                        \Filament\Forms\Components\FileUpload::make('proof_of_delivery')
                            ->label('Foto Bukti Pengiriman (Opsional)')
                            ->image()
                            ->directory('shipment-proofs')
                            ->nullable(),
                    ])
                    ->action(function (Shipment $record, array $data): void {
                        if (!empty($data['proof_of_delivery'])) {
                            $record->update(['proof_of_delivery' => $data['proof_of_delivery']]);
                        }
                        app(ShipmentService::class)->updateStatus($record, ShipmentStatus::SHIPPED);
                        Notification::make()->title('Status diperbarui: Dikirim')->success()->send();
                    }),

                \Filament\Actions\Action::make('mark_delivered')
                    ->label('Tandai Diterima')
                    ->icon('heroicon-m-check-badge')
                    ->color('success')
                    ->visible(fn (Shipment $record) => $record->status === ShipmentStatus::SHIPPED)
                    ->requiresConfirmation()
                    ->form([
                        \Filament\Forms\Components\FileUpload::make('proof_of_delivery')
                            ->label('Foto Bukti Pengiriman (Opsional)')
                            ->image()
                            ->directory('shipment-proofs')
                            ->nullable(),
                    ])
                    ->action(function (Shipment $record, array $data): void {
                        if (!empty($data['proof_of_delivery'])) {
                            $record->update(['proof_of_delivery' => $data['proof_of_delivery']]);
                        }
                        app(ShipmentService::class)->updateStatus($record, ShipmentStatus::DELIVERED);
                        Notification::make()->title('Status diperbarui: Diterima')->success()->send();
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
            'index'  => Pages\ListShipments::route('/'),
            'create' => Pages\CreateShipment::route('/create'),
            'view'   => Pages\ViewShipment::route('/{record}'),
        ];
    }
}
