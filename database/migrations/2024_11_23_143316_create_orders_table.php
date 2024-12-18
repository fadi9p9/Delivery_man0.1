<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('orderId');
            $table->enum('status', ['Active', 'Canceled', 'Pending','Done'])->default('Pending');
            $table->foreignId('cartId')->constrained('carts')->onDelete('cascade');
            $table->string('orderLocation', 255)->nullable();
            $table->foreignId('customerId')->constrained('users')->onDelete('cascade');
            $table->foreignId('deliveryId')->nullable()->constrained('users')->onDelete('cascade');
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
