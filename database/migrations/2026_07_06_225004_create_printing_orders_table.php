<?php

use App\Enums\PrintingOrderStatus;
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
        Schema::create('printing_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekretaris_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('percetakan_id')->constrained('percetakans')->cascadeOnDelete();
            $table->string('order_code')->unique();
            $table->string('status')->default(PrintingOrderStatus::DRAFT->value);
            $table->date('order_date');
            $table->date('target_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('printing_orders');
    }
};
