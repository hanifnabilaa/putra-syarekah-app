<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BillResource;
use App\Models\Bill;
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $daerah = $request->user()->daerah;

        $bills = Bill::whereHas('order', fn ($q) => $q->where('daerah_id', $daerah->id))
            ->with(['order', 'payments.confirmedBy'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate($request->per_page ?? 10);

        return BillResource::collection($bills);
    }

    public function show(Request $request, Bill $bill)
    {
        $daerah = $request->user()->daerah;

        if ($bill->order->daerah_id !== $daerah->id) {
            return response()->json(['message' => 'Tidak ditemukan.'], 404);
        }

        $bill->load(['order.items.product', 'payments.confirmedBy']);

        return new BillResource($bill);
    }
}
