<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // ডিফল্ট মান: VAT হার Settings পেজ থেকে বদলাবেন (আপনার জন্য প্রযোজ্য হার যাচাই করে নিন)
        $now = now();
        DB::table('settings')->insert([
            ['key' => 'restaurant_name', 'value' => 'Restaurant POS',               'created_at' => $now, 'updated_at' => $now],
            ['key' => 'address',         'value' => '',                              'created_at' => $now, 'updated_at' => $now],
            ['key' => 'phone',           'value' => '',                              'created_at' => $now, 'updated_at' => $now],
            ['key' => 'vat_rate',        'value' => '5',                             'created_at' => $now, 'updated_at' => $now],
            ['key' => 'receipt_footer',  'value' => 'Thank you for dining with us!', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
