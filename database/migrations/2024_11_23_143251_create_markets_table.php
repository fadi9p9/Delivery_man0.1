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
    Schema::create('markets', function (Blueprint $table) {
        $table->id('marketId');
        $table->foreignId('userId')->constrained('users')->onDelete('cascade');
        $table->string('title', 100);
        $table->string('location', 255)->nullable();
        $table->string('img', 255)->nullable();
        $table->timestamps();
        // rate 
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('markets');
    }
};
