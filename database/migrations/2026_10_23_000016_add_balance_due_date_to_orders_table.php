<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->date('balance_due_date')->nullable()->after('deposit_total');
            $table->timestamp('balance_reminder_sent_at')->nullable()->after('balance_due_date');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['balance_due_date', 'balance_reminder_sent_at']);
        });
    }
};
