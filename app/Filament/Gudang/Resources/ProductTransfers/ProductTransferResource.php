<?php

namespace App\Filament\Gudang\Resources\ProductTransfers;

use App\Filament\Gudang\Resources\ProductTransfers\Pages\CreateProductTransfer;
use App\Filament\Gudang\Resources\ProductTransfers\Pages\ListProductTransfers;
use App\Filament\Gudang\Resources\ProductTransfers\Pages\ViewProductTransfer;
use App\Filament\Gudang\Resources\ProductTransfers\Schemas\ProductTransferForm;

use App\Filament\Gudang\Resources\ProductTransfers\Tables\ProductTransfersTable;
use App\Models\ProductTransfer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProductTransferResource extends Resource
{
    protected static ?string $model = ProductTransfer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ProductTransferForm::configure($schema);
    }



    public static function table(Table $table): Table
    {
        return ProductTransfersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->where(function ($query) {
                $query->where('from_gudang_id', auth()->user()->getMasterId())
                      ->orWhere('to_gudang_id', auth()->user()->getMasterId());
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductTransfers::route('/'),
            'create' => CreateProductTransfer::route('/create'),
            'view' => ViewProductTransfer::route('/{record}'),
        ];
    }
}
