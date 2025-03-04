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
            $table->string('mobile_no');
            $table->string('email')->nullable();
            $table->string('vehicle_number');
            $table->enum('claim_status', ['yes', 'no']);
            $table->enum('policy_type', ['New', 'Fresh', 'Renewal']);
            $table->boolean('is_issue')->default(false);
            $table->boolean('is_zm_verified')->default(false);
            $table->boolean('is_retail_verified')->default(false);
            $table->boolean('is_cancel')->default(false);
            $table->boolean('is_accepted')->default(false);
            $table->string('payment_link')->nullable();
            $table->string('payment_receipt')->nullable();
            $table->boolean('is_payment_complete')->default(false);
            $table->boolean('final_status')->default(false);
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
