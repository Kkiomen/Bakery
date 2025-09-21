<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('b2_b_orders', function (Blueprint $table) {
            $table->foreignId('recurring_order_id')->nullable()->after('b2_b_client_id')->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('b2_b_orders', function (Blueprint $table) {
            $table->dropForeign(['recurring_order_id']);
            $table->dropColumn('recurring_order_id');
        });
    }
};
