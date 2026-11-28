<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_no')->nullable()->unique();           // SRT-000001
            $table->foreignId('order_id')->constrained('orders')->restrictOnDelete();
            $table->decimal('subtotal', 12, 2);                           // ফেরত আইটেমের দাম (ছাড়ের আগে)
            $table->decimal('discount_amount', 12, 2)->default(0);        // ওই অংশের ছাড়
            $table->decimal('vat_amount', 12, 2)->default(0);             // ওই অংশের VAT
            $table->decimal('refund_amount', 12, 2);                      // কাস্টমারকে ফেরত
            $table->string('refund_method', 20);                          // cash / card / bkash ...
            $table->string('reason', 30);
            $table->boolean('restock')->default(false);                   // ইনগ্রেডিয়েন্ট স্টকে ফেরত গেছে কিনা
            $table->string('note')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index('created_at');
        });

        Schema::create('sales_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_return_id')->constrained('sales_returns')->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained('order_items')->restrictOnDelete();
            $table->string('item_name');
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('unit_cost', 12, 4)->default(0);
            $table->decimal('line_total', 12, 2);
            $table->json('restocked')->nullable();                        // স্টকে ফেরত যাওয়া ইনগ্রেডিয়েন্ট
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_return_items');
        Schema::dropIfExists('sales_returns');
    }
};
