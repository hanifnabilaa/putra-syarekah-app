<?php

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
        Schema::table('products', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE products MODIFY image LONGTEXT NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE products MODIFY image VARCHAR(255) NULL');
        });
    }
};
