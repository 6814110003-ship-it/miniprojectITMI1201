<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no')->unique();
            $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 20)->default('pending_payment')->index();
            $table->string('customer_name');
            $table->string('customer_phone', 20);
            $table->text('shipping_address');
            $table->string('shipping_method', 50)->default('ems');
            $table->unsignedInteger('shipping_fee')->default(0);
            $table->unsignedInteger('subtotal')->default(0);
            $table->unsignedInteger('deposit_total')->default(0);
            $table->unsignedInteger('total')->default(0);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};