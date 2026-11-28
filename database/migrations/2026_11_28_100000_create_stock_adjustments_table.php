<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->constrained('ingredients')->restrictOnDelete();
            $table->string('direction', 3);                      // in = স্টক বাড়ল, out = স্টক কমল
            $table->decimal('quantity', 12, 3);
            $table->string('reason', 30);                        // wastage, expired, damaged, ...
            $table->decimal('unit_cost', 12, 4)->default(0);     // ওই সময়ের গড় খরচ (snapshot)
            $table->decimal('stock_before', 12, 3);
            $table->decimal('stock_after', 12, 3);
            $table->string('note')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['ingredient_id', 'created_at']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
