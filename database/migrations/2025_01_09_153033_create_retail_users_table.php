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
        Schema::create('retail_users', function (Blueprint $table) {
            $table->id();  // auto-increment primary key
            $table->unsignedBigInteger('user_id')->nullable();  // This is the column for the foreign key
            $table->string('name');
            $table->timestamps();
    
            // Add the foreign key constraint
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retail_users');
    }
};
