<?php

// database/migrations/XXXX_add_electric_bill_columns_to_lease_payments.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lease_payments', function (Blueprint $table) {

            if (! Schema::hasColumn('lease_payments', 'electric_bill_amount')) {
                $table->decimal('electric_bill_amount', 10, 2)->default(0)->after('amount');
            }

            $table->foreignId('electric_bill_id')->nullable()->after('electric_bill_amount');
            $table->foreign('electric_bill_id')
                ->references('id')
                ->on('electric_bills')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('lease_payments', function (Blueprint $table) {
            $table->dropForeign(['electric_bill_id']);
            $table->dropColumn('electric_bill_id');

        });
    }
};
