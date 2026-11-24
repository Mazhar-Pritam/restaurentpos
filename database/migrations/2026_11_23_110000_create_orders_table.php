<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no')->nullable()->unique();           // ORD-000001
            $table->string('type', 20);                                  // dine_in / takeaway
            $table->foreignId('table_id')->nullable()->constrained('restaurant_tables')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->decimal('subtotal', 12, 2);
            $table->string('discount_type', 10)->nullable();             // percent / flat
            $table->decimal('discount_value', 12, 2)->default(0);        // ক্যাশিয়ারের দেওয়া মান (10% বা ৳50)
            $table->decimal('discount_amount', 12, 2)->default(0);       // আসল ছাড়ের টাকা
            $table->decimal('vat_rate', 5, 2)->default(0);
            $table->decimal('vat_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->string('payment_method', 20);                        // cash / card / bkash ...
            $table->string('status', 20)->default('cooking');            // cooking / ready / completed
            $table->string('note')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index('created_at');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('menu_item_id')->constrained('menu_items')->restrictOnDelete();
            $table->string('item_name');                                  // বিক্রির সময়ের নাম (পরে বদলালেও রিসিট ঠিক থাকে)
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('unit_cost', 12, 4)->default(0);              // রেসিপি অনুযায়ী প্রতি পিসের খরচ (লাভের হিসাবের জন্য)
            $table->decimal('line_total', 12, 2);
            $table->json('consumption')->nullable();                      // কোন ইনগ্রেডিয়েন্ট কতটুকু কমেছে (void করলে ফেরত আসে)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
