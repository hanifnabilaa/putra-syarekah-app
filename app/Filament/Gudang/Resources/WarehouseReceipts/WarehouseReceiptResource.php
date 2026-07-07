<?php

namespace App\Filament\Gudang\Resources\WarehouseReceipts;

use App\Filament\Gudang\Resources\WarehouseReceipts\Pages\CreateWarehouseReceipt;
use App\Filament\Gudang\Resources\WarehouseReceipts\Pages\EditWarehouseReceipt;
use App\Filament\Gudang\Resources\WarehouseReceipts\Pages\ListWarehouseReceipts;
use App\Filament\Gudang\Resources\WarehouseReceipts\Schemas\WarehouseReceiptForm;
use App\Filament\Gudang\Resources\WarehouseReceipts\Tables\WarehouseReceiptsTable;
use App\Models\WarehouseReceipt;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WarehouseReceiptResource extends Resource
{
    protected static ?string $model = WarehouseReceipt::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return WarehouseReceiptForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WarehouseReceiptsTable::configure($table);
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
            'index' => ListWarehouseReceipts::route('/'),
            'create' => CreateWarehouseReceipt::route('/create'),
            'edit' => EditWarehouseReceipt::route('/{record}/edit'),
        ];
    }
}
