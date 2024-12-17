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
        Schema::create('order_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cartItemId');
            $table->unsignedBigInteger('cartId')->nullable();
            $table->unsignedBigInteger('productId');
            $table->integer('decremented_quantity');
            $table->timestamps();
            $table->timestamp('expires_at')->nullable(); // عمود مدة التراجع
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_logs');
    }
};