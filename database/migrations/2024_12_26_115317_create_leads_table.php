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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('zm_id')->nullable(); 
            $table->foreign('zm_id')->references('id')->on('zonal_managers')->onDelete('set null');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('gender');
            $table->date('date_of_birth');
            $table->string('mobile_no');
            $table->string('vehicle_number');
            $table->boolean('is_doc_complete')->default(true);
            $table->boolean('is_zm_verified')->default(false);
            $table->boolean('is_payment_complete')->default(false);
            $table->boolean('is_cancel')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
