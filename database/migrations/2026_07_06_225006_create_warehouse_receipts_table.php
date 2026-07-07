<?php

use App\Enums\WarehouseReceiptStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('warehouse_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('printing_order_id')->constrained('printing_orders')->cascadeOnDelete();
            $table->foreignId('gudang_id')->constrained('users')->cascadeOnDelete();
            $table->string('receipt_code')->unique();
            $table->string('status')->default(WarehouseReceiptStatus::BELUM_PRODUKSI->value);
            $table->date('receipt_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_receipts');
    }
};
