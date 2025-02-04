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
        Schema::table('quotes', callback: function (Blueprint $table) {
           $table->decimal('od_premium',10,2)->after('price');
           $table->decimal('tp_premium',10,2)->after('od_premium');
           $table->decimal('vehicle_idv',10,2)->after('tp_premium');
           $table->date('policy_start_date')->nullable()->after('vehicle_idv');
           $table->date('policy_end_date')->nullable()->after('policy_start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn('od_premium');
            $table->dropColumn('vehicle_idv');
            $table->dropColumn('tp_premium');
            $table->dropColumn('policy_start_date');
            $table->dropColumn('policy_end_date');
        });
    }
};
