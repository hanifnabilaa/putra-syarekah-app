<?php

namespace App\Filament\Percetakan\Resources\DaerahResource\Pages;

use App\Filament\Percetakan\Resources\DaerahResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDaerahs extends ListRecords
{
    protected static string $resource = DaerahResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->label('Tambah Daerah')];
    }
}
