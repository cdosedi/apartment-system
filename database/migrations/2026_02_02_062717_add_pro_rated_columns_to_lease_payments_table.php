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
        Schema::table('lease_payments', function (Blueprint $table) {
            $table->boolean('is_pro_rated')->default(false)->after('status');
            $table->integer('pro_rated_days')->nullable()->after('is_pro_rated');
            $table->integer('pro_rated_total_days')->nullable()->after('pro_rated_days');
        });
    }

    public function down(): void
    {
        Schema::table('lease_payments', function (Blueprint $table) {
            $table->dropColumn(['is_pro_rated', 'pro_rated_days', 'pro_rated_total_days']);
        });
    }
};
