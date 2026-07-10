<?php

namespace App\Filament\Sekretaris\Resources\Orders\Pages;

use App\Enums\OrderStatus;
use App\Filament\Sekretaris\Resources\Orders\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('Setujui Pesanan')
                ->icon('heroicon-m-check-circle')
                ->color('success')
                ->visible(fn (Order $record) => $record->status === OrderStatus::SUBMITTED)
                ->requiresConfirmation()
                ->modalHeading('Approve Pesanan?')
                ->modalDescription(fn (Order $record) => "Anda akan menyetujui pesanan {$record->order_code} dari {$record->daerah->name}. Total tagihan: Rp " . number_format($record->total_bill, 0, ',', '.'))
                ->action(function (Order $record): void {
                    app(OrderService::class)->approveOrder($record);
                    Notification::make()
                        ->title('Pesanan disetujui')
                        ->body("Pesanan {$record->order_code} berhasil disetujui.")
                        ->success()
                        ->send();
                    $this->redirect(OrderResource::getUrl('index'));
                }),

            Action::make('reject')
                ->label('Tolak Pesanan')
                ->icon('heroicon-m-x-circle')
                ->color('danger')
                ->visible(fn (Order $record) => $record->status === OrderStatus::SUBMITTED)
                ->form([
                    Textarea::make('rejection_reason')
                        ->label('Alasan Penolakan')
                        ->required()
                        ->rows(3)
                        ->placeholder('Masukkan alasan mengapa pesanan ditolak...'),
                ])
                ->action(function (Order $record, array $data): void {
                    app(OrderService::class)->rejectOrder($record, $data['rejection_reason']);
                    Notification::make()
                        ->title('Pesanan ditolak')
                        ->body("Pesanan {$record->order_code} telah ditolak.")
                        ->warning()
                        ->send();
                    $this->redirect(OrderResource::getUrl('index'));
                }),
        ];
    }
}
