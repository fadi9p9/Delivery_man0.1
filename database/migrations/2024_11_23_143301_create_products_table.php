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
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->foreignId('subcategoryId')->constrained('subcategories')->onDelete('cascade');
        $table->string('title', 100);
        $table->text('description')->nullable();
        $table->decimal('price', 10, 2);
        $table->decimal('discount', 5, 2)->nullable();
        $table->string('size', 50)->nullable();
        $table->integer('totalQuantity')->nullable();
        $table->decimal('rate', 3, 2)->default(0);
        $table->timestamps();
        // market id 
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
