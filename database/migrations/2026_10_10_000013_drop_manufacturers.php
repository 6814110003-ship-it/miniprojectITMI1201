<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['manufacturer_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('manufacturer_id');
        });

        Schema::dropIfExists('manufacturers');
    }

    public function down(): void
    {
        Schema::create('manufacturers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('manufacturer_id')->nullable()->constrained()->nullOnDelete();
        });
    }
};
