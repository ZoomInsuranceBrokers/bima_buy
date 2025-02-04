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
        Schema::table('leads', function (Blueprint $table) {
            $table->enum('vehicle_type', ['Motorcycle', 'Private Car', 'Commercial Vehicle'])->nullable()->after('email');
            $table->boolean('ask_another_quotes')->default(false)->after('is_retail_verified');
            $table->boolean('quotes_send')->default(false)->after('ask_another_quotes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Dropping the columns in reverse order to avoid dependency issues
            $table->dropColumn('quotes_send');
            $table->dropColumn('ask_another_quotes');
            $table->dropColumn('vehicle_type');
        });
    }
};
