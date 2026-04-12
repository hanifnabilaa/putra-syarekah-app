<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    public function index(Request $request)
    {
        $daerah = $request->user()->daerah;

        $orders = Order::forDaerah($daerah->id)
            ->with(['items.product', 'bill', 'shipments'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate($request->per_page ?? 10);

        return OrderResource::collection($orders);
    }

    public function store(StoreOrderRequest $request)
    {
        $daerah = $request->user()->daerah;
        $order  = $this->orderService->createOrder($daerah, $request->validated());

        return new OrderResource($order);
    }

    public function show(Request $request, Order $order)
    {
        $daerah = $request->user()->daerah;

        if ($order->daerah_id !== $daerah->id) {
            return response()->json(['message' => 'Tidak ditemukan.'], 404);
        }

        $order->load(['items.product', 'items.shipmentItems', 'shipments.items.orderItem.product', 'bill.payments.confirmedBy']);

        return new OrderResource($order);
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        $daerah = $request->user()->daerah;

        if ($order->daerah_id !== $daerah->id) {
            return response()->json(['message' => 'Tidak ditemukan.'], 404);
        }

        if (! $order->canBeEditedByDaerah()) {
            return response()->json(['message' => 'Pesanan tidak dapat diubah setelah disetujui.'], 422);
        }

        $order = $this->orderService->updateOrder($order, $request->validated());

        return new OrderResource($order);
    }

    public function destroy(Request $request, Order $order)
    {
        $daerah = $request->user()->daerah;

        if ($order->daerah_id !== $daerah->id) {
            return response()->json(['message' => 'Tidak ditemukan.'], 404);
        }

        if (! $order->canBeEditedByDaerah()) {
            return response()->json(['message' => 'Pesanan tidak dapat dibatalkan.'], 422);
        }

        $this->orderService->cancelOrder($order);

        return response()->json(['message' => 'Pesanan berhasil dibatalkan.']);
    }
}
