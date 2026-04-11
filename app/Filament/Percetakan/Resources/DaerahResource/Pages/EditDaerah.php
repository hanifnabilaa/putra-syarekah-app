<?php

namespace App\Filament\Percetakan\Resources\DaerahResource\Pages;

use App\Filament\Percetakan\Resources\DaerahResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDaerah extends EditRecord
{
    protected static string $resource = DaerahResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
