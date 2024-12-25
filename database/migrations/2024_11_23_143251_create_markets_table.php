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
        $table->id();
        $table->foreignId('userId')->constrained('users')->onDelete('cascade');
        $table->string('title', 100);
        $table->text('description')->nullable();
        $table->string('location', 255)->nullable();
        $table->string('img')->nullable();
        $table->float('rating')->default(0);
        $table->unsignedInteger('rating_count')->default(0);
        $table->timestamps();
        
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
