<?php

namespace App\Filament\Sekretaris\Resources\PrintingOrders;

use App\Filament\Sekretaris\Resources\PrintingOrders\Pages\CreatePrintingOrder;
use App\Filament\Sekretaris\Resources\PrintingOrders\Pages\EditPrintingOrder;
use App\Filament\Sekretaris\Resources\PrintingOrders\Pages\ListPrintingOrders;
use App\Filament\Sekretaris\Resources\PrintingOrders\Schemas\PrintingOrderForm;
use App\Filament\Sekretaris\Resources\PrintingOrders\Tables\PrintingOrdersTable;
use App\Models\PrintingOrder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PrintingOrderResource extends Resource
{
    protected static ?string $model = PrintingOrder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PrintingOrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrintingOrdersTable::configure($table);
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
            'index' => ListPrintingOrders::route('/'),
            'create' => CreatePrintingOrder::route('/create'),
            'edit' => EditPrintingOrder::route('/{record}/edit'),
        ];
    }
}
