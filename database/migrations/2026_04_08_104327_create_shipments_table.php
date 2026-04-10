<?php

use App\Enums\ShipmentStatus;
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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->integer('queue_order')->default(0);
            $table->string('status')->default(ShipmentStatus::PENDING->value);
            $table->string('method')->default(ShipmentStatus::PENDING->value);
            $table->text('shipping_address')->nullable();
            $table->date('shipping_date')->nullable();
            $table->text('notes')->nullable();
            $table->index(['order_id', 'status']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
