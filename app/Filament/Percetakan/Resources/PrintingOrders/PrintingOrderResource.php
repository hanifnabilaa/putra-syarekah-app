<?php

namespace App\Filament\Percetakan\Resources\PrintingOrders;

use App\Filament\Percetakan\Resources\PrintingOrders\Pages\CreatePrintingOrder;
use App\Filament\Percetakan\Resources\PrintingOrders\Pages\EditPrintingOrder;
use App\Filament\Percetakan\Resources\PrintingOrders\Pages\ListPrintingOrders;
use App\Filament\Percetakan\Resources\PrintingOrders\Schemas\PrintingOrderForm;
use App\Filament\Percetakan\Resources\PrintingOrders\Tables\PrintingOrdersTable;
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
            'edit' => EditPrintingOrder::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->whereHas('percetakan', function ($query) {
            $query->where('user_id', auth()->user()->getMasterId());
        });
    }
}
