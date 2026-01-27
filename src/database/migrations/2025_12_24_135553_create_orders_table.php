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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->restrictOnDelete();
            $table->foreignId('buyer_user_id')->constrained('users')->restrictOnDelete();
            $table->tinyInteger('status')->default(0)->comment('0:購入未確定, 1:購入確定, 2:キャンセル');
            $table->string('postal_code', 8);
            $table->string('address', 255);
            $table->string('building', 255)->nullable();
            $table->timestamps();

            // 1つの商品は1回しか注文できないようにする
            $table->unique('item_id', 'orders_item_unique');
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
