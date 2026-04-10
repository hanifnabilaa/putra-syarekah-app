<?php

use App\Enums\OrderStatus;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daerah_id')->constrained('daerahs')->cascadeOnDelete();
            $table->string('order_code')->unique();
            $table->string('status')->default(OrderStatus::DRAFT->value);
            $table->string('shipping_method');
            $table->text('shipping_address')->nullable();
            $table->decimal('total_bill', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->index(['daerah_id', 'status']);
            $table->index('created_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
